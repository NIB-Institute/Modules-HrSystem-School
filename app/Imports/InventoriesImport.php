<?php

namespace Modules\School\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\School\Models\Classroom;
use Modules\School\Models\Department;
use Modules\School\Models\Equipment;
use Modules\School\Models\Inventory;

/**
 * Excel/CSV import for the Inventory module. Mirrors the EquipmentImport
 * pattern: collects rows, supports preview mode, supports three duplicate
 * strategies (skip / update / fail), and reports per-row errors.
 *
 * Resolves Equipment, Classroom, and Department by *name* so a non-technical
 * user can fill the spreadsheet without knowing FKs.
 */
class InventoriesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithBatchInserts, WithChunkReading
{
    public const DUPLICATE_SKIP = 'skip';
    public const DUPLICATE_UPDATE = 'update';
    public const DUPLICATE_FAIL = 'fail';

    protected array $errors = [];
    protected array $failedRows = [];
    protected int $importedCount = 0;
    protected int $updatedCount = 0;
    protected int $skippedCount = 0;
    protected string $duplicateHandling;
    protected bool $previewMode = false;
    protected array $previewData = [];

    public function __construct(string $duplicateHandling = self::DUPLICATE_SKIP, bool $previewMode = false)
    {
        $this->duplicateHandling = $duplicateHandling;
        $this->previewMode = $previewMode;
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            $this->errors[] = ['row' => 0, 'message' => 'No data rows found.'];

            return;
        }

        if ($this->previewMode) {
            $this->processPreview($rows);

            return;
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $this->processRow($row->toArray(), $index + 2);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->errors[] = ['row' => 0, 'message' => "Import failed: {$e->getMessage()}"];
        }
    }

    protected function processPreview(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $data = $this->normalizeRow($row->toArray());

            $preview = [
                'row_number' => $rowNumber,
                'asset_tag' => $data['asset_tag'] ?? null,
                'serial_number' => $data['serial_number'] ?? null,
                'equipment' => $data['equipment'] ?? null,
                'classroom' => $data['classroom'] ?? null,
                'department' => $data['department'] ?? null,
                'status' => 'ready',
                'errors' => [],
                'warnings' => [],
                'is_duplicate' => false,
                'existing_record' => null,
            ];

            $validator = Validator::make($data, [
                'asset_tag' => 'required|string|max:50',
                'equipment' => 'required|string|max:150',
            ]);

            if ($validator->fails()) {
                $preview['status'] = 'error';
                $preview['errors'] = $validator->errors()->all();
            }

            if (! empty($data['equipment']) && ! $this->resolveEquipmentId($data['equipment'])) {
                $preview['status'] = 'error';
                $preview['errors'][] = "Equipment '{$data['equipment']}' not found.";
            }

            if (! empty($data['asset_tag'])) {
                $existing = Inventory::withoutGlobalScopes()
                    ->where('asset_tag', $data['asset_tag'])
                    ->first();
                if ($existing) {
                    $preview['is_duplicate'] = true;
                    $preview['existing_record'] = ['id' => $existing->id, 'asset_tag' => $existing->asset_tag];

                    if ($this->duplicateHandling === self::DUPLICATE_FAIL) {
                        $preview['status'] = 'error';
                        $preview['errors'][] = "Duplicate asset tag: {$data['asset_tag']}";
                    } elseif ($this->duplicateHandling === self::DUPLICATE_SKIP) {
                        $preview['status'] = 'skip';
                        $preview['warnings'][] = 'Will be skipped (duplicate)';
                    } else {
                        $preview['status'] = 'update';
                        $preview['warnings'][] = 'Will update existing record';
                    }
                }
            }

            $this->previewData[] = $preview;
        }
    }

    protected function processRow(array $row, int $rowNumber): void
    {
        $data = $this->normalizeRow($row);

        $validator = Validator::make($data, [
            'asset_tag' => 'required|string|max:50',
            'equipment' => 'required|string|max:150',
        ]);

        if ($validator->fails()) {
            $this->addFailedRow($rowNumber, $data, $validator->errors()->all());

            return;
        }

        $equipmentId = $this->resolveEquipmentId($data['equipment']);
        if (! $equipmentId) {
            $this->addFailedRow($rowNumber, $data, ["Equipment '{$data['equipment']}' not found."]);

            return;
        }

        $existing = Inventory::withoutGlobalScopes()
            ->where('asset_tag', $data['asset_tag'])
            ->first();

        if ($existing) {
            switch ($this->duplicateHandling) {
                case self::DUPLICATE_FAIL:
                    $this->addFailedRow($rowNumber, $data, ["Asset tag '{$data['asset_tag']}' already exists."]);

                    return;
                case self::DUPLICATE_SKIP:
                    $this->skippedCount++;

                    return;
                case self::DUPLICATE_UPDATE:
                    $this->updateRecord($existing, $data, $equipmentId, $rowNumber);

                    return;
            }
        }

        $this->createRecord($data, $equipmentId, $rowNumber);
    }

    protected function createRecord(array $data, int $equipmentId, int $rowNumber): void
    {
        try {
            Inventory::create([
                'uuid' => (string) Str::uuid(),
                'asset_tag' => $data['asset_tag'],
                'serial_number' => $data['serial_number'] ?? null,
                'name' => $data['name'] ?? null,
                'equipment_id' => $equipmentId,
                'classroom_id' => $this->resolveClassroomId($data['classroom'] ?? null),
                'department_id' => $this->resolveDepartmentId($data['department'] ?? null),
                'status' => $this->normalizeStatus($data['status'] ?? Inventory::STATUS_IN_STOCK),
                'condition' => $this->normalizeCondition($data['condition'] ?? Inventory::CONDITION_GOOD),
                'cost' => $data['cost'] ?? null,
                'vendor' => $data['vendor'] ?? null,
                'purchased_at' => $data['purchased_at'] ?? null,
                'warranty_until' => $data['warranty_until'] ?? null,
                'notes' => $data['notes'] ?? null,
                'is_active' => $this->parseBool($data['is_active'] ?? 'yes'),
            ]);

            $this->importedCount++;
        } catch (\Throwable $e) {
            $this->addFailedRow($rowNumber, $data, [$e->getMessage()]);
        }
    }

    protected function updateRecord(Inventory $record, array $data, int $equipmentId, int $rowNumber): void
    {
        try {
            $record->update([
                'serial_number' => $data['serial_number'] ?? $record->serial_number,
                'name' => $data['name'] ?? $record->name,
                'equipment_id' => $equipmentId,
                'classroom_id' => $this->resolveClassroomId($data['classroom'] ?? null) ?? $record->classroom_id,
                'department_id' => $this->resolveDepartmentId($data['department'] ?? null) ?? $record->department_id,
                'status' => $this->normalizeStatus($data['status'] ?? $record->status),
                'condition' => $this->normalizeCondition($data['condition'] ?? $record->condition),
                'cost' => $data['cost'] ?? $record->cost,
                'vendor' => $data['vendor'] ?? $record->vendor,
                'purchased_at' => $data['purchased_at'] ?? $record->purchased_at,
                'warranty_until' => $data['warranty_until'] ?? $record->warranty_until,
                'notes' => $data['notes'] ?? $record->notes,
                'is_active' => isset($data['is_active'])
                    ? $this->parseBool($data['is_active'])
                    : $record->is_active,
            ]);
            $this->updatedCount++;
        } catch (\Throwable $e) {
            $this->addFailedRow($rowNumber, $data, [$e->getMessage()]);
        }
    }

    protected function resolveEquipmentId(?string $name): ?int
    {
        if (empty($name)) {
            return null;
        }

        return Equipment::where('name', $name)->value('id');
    }

    protected function resolveClassroomId(?string $name): ?int
    {
        if (empty($name)) {
            return null;
        }

        return Classroom::where('name', $name)->value('id');
    }

    protected function resolveDepartmentId(?string $name): ?int
    {
        if (empty($name)) {
            return null;
        }

        return Department::where('name', $name)->value('id');
    }

    protected function normalizeStatus(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        $allowed = array_keys(Inventory::statuses());

        return in_array($value, $allowed, true) ? $value : Inventory::STATUS_IN_STOCK;
    }

    protected function normalizeCondition(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        $allowed = array_keys(Inventory::conditions());

        return in_array($value, $allowed, true) ? $value : Inventory::CONDITION_GOOD;
    }

    protected function parseBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        $value = strtolower(trim((string) $value));

        return in_array($value, ['1', 'true', 'yes', 'active', 'enabled'], true);
    }

    protected function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalizedKey = Str::snake(str_replace(' ', '_', strtolower(trim($key))));
            $normalized[$normalizedKey] = is_string($value) ? trim($value) : $value;
        }

        return $normalized;
    }

    protected function addFailedRow(int $rowNumber, array $data, array $errors): void
    {
        $this->failedRows[] = ['row_number' => $rowNumber, 'data' => $data, 'errors' => $errors];
        $this->skippedCount++;
    }

    public function getPreviewData(): array
    {
        return $this->previewData;
    }

    public function getResults(): array
    {
        $stats = ['total' => count($this->previewData), 'ready' => 0, 'update' => 0, 'skip' => 0, 'error' => 0];
        foreach ($this->previewData as $row) {
            $status = $row['status'] ?? 'ready';
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }

        return [
            'imported' => $this->importedCount,
            'updated' => $this->updatedCount,
            'skipped' => $this->skippedCount,
            'failed' => count($this->failedRows),
            'failed_rows' => $this->failedRows,
            'preview_stats' => $stats,
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
