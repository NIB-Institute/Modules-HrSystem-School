<?php

namespace Modules\School\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\School\Models\Equipment;

class EquipmentImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithBatchInserts, WithChunkReading
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
        } catch (\Exception $e) {
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
                'name' => $data['name'] ?? null,
                'category' => $data['category'] ?? null,
                'icon' => $data['icon'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => 'ready',
                'errors' => [],
                'warnings' => [],
                'is_duplicate' => false,
                'existing_record' => null,
            ];

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                $preview['status'] = 'error';
                $preview['errors'] = $validator->errors()->all();
            }

            $slug = Str::slug($data['name'] ?? '');
            if (!empty($slug)) {
                $existing = Equipment::withoutGlobalScopes()->where('slug', $slug)->first();
                if ($existing) {
                    $preview['is_duplicate'] = true;
                    $preview['existing_record'] = ['id' => $existing->id, 'name' => $existing->name];

                    if ($this->duplicateHandling === self::DUPLICATE_FAIL) {
                        $preview['status'] = 'error';
                        $preview['errors'][] = "Duplicate: {$data['name']}";
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
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            $this->addFailedRow($rowNumber, $data, $validator->errors()->all());
            return;
        }

        $slug = Str::slug($data['name']);
        $existing = Equipment::withoutGlobalScopes()->where('slug', $slug)->first();

        if ($existing) {
            switch ($this->duplicateHandling) {
                case self::DUPLICATE_FAIL:
                    $this->addFailedRow($rowNumber, $data, ["'{$data['name']}' already exists"]);
                    return;
                case self::DUPLICATE_SKIP:
                    $this->skippedCount++;
                    return;
                case self::DUPLICATE_UPDATE:
                    $this->updateRecord($existing, $data, $rowNumber);
                    return;
            }
        }

        $this->createRecord($data, $rowNumber);
    }

    protected function createRecord(array $data, int $rowNumber): void
    {
        try {
            Equipment::create([
                'uuid' => (string) Str::uuid(),
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'category' => $this->normalizeCategory($data['category'] ?? 'other'),
                'icon' => $data['icon'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $this->parseStatus($data['status'] ?? 'active'),
            ]);

            $this->importedCount++;
        } catch (\Exception $e) {
            $this->addFailedRow($rowNumber, $data, [$e->getMessage()]);
        }
    }

    protected function updateRecord(Equipment $record, array $data, int $rowNumber): void
    {
        try {
            $record->update([
                'name' => $data['name'],
                'category' => $this->normalizeCategory($data['category'] ?? $record->category),
                'icon' => $data['icon'] ?? $record->icon,
                'description' => $data['description'] ?? $record->description,
                'status' => $this->parseStatus($data['status'] ?? 'active'),
            ]);

            $this->updatedCount++;
        } catch (\Exception $e) {
            $this->addFailedRow($rowNumber, $data, [$e->getMessage()]);
        }
    }

    protected function normalizeCategory(?string $value): string
    {
        if (empty($value)) return 'other';
        $value = strtolower(trim($value));
        return in_array($value, ['technology', 'furniture', 'safety', 'accessibility', 'other'])
            ? $value : 'other';
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

    protected function parseStatus($value): bool
    {
        if (is_bool($value)) return $value;
        $value = strtolower(trim((string) $value));
        return in_array($value, ['1', 'true', 'yes', 'active', 'enabled']);
    }

    protected function addFailedRow(int $rowNumber, array $data, array $errors): void
    {
        $this->failedRows[] = ['row_number' => $rowNumber, 'data' => $data, 'errors' => $errors];
        $this->skippedCount++;
    }

    public function getPreviewData(): array { return $this->previewData; }

    public function getResults(): array
    {
        $stats = ['total' => count($this->previewData), 'ready' => 0, 'update' => 0, 'skip' => 0, 'error' => 0];
        foreach ($this->previewData as $row) {
            $status = $row['status'] ?? 'ready';
            if (isset($stats[$status])) $stats[$status]++;
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

    public function batchSize(): int { return 100; }
    public function chunkSize(): int { return 100; }
}
