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
use Modules\School\Models\Classroom;
use Modules\School\Models\Department;

class ClassroomsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithBatchInserts, WithChunkReading
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

            // Resolve department name for preview
            $departmentName = $data['department'] ?? null;
            if ($departmentName) {
                $dept = Department::withoutGlobalScopes()->where('name', $departmentName)->first();
                if (!$dept) {
                    $departmentName = $departmentName . ' (not found)';
                }
            }

            $preview = [
                'row_number' => $rowNumber,
                'name' => $data['name'] ?? null,
                'code' => $data['code'] ?? null,
                'department' => $departmentName,
                'building' => $data['building'] ?? null,
                'capacity' => $data['capacity'] ?? null,
                'status' => 'ready',
                'errors' => [],
                'warnings' => [],
                'is_duplicate' => false,
                'existing_record' => null,
            ];

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                $preview['status'] = 'error';
                $preview['errors'] = $validator->errors()->all();
            }

            // Validate department if specified
            if (!empty($data['department'])) {
                $dept = Department::withoutGlobalScopes()->where('name', $data['department'])->first();
                if (!$dept) {
                    $preview['status'] = 'error';
                    $preview['errors'][] = "Department '{$data['department']}' not found. Please check the department name matches exactly.";
                }
            }

            if (!empty($data['code'])) {
                $existing = Classroom::withoutGlobalScopes()->where('code', $data['code'])->first();
                if ($existing) {
                    $preview['is_duplicate'] = true;
                    $preview['existing_record'] = ['id' => $existing->id, 'name' => $existing->name];

                    if ($this->duplicateHandling === self::DUPLICATE_FAIL) {
                        $preview['status'] = 'error';
                        $preview['errors'][] = "Duplicate code: {$data['code']}";
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
            'code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            $this->addFailedRow($rowNumber, $data, $validator->errors()->all());
            return;
        }

        // Validate department if specified
        if (!empty($data['department'])) {
            $dept = Department::withoutGlobalScopes()->where('name', $data['department'])->first();
            if (!$dept) {
                $this->addFailedRow($rowNumber, $data, ["Department '{$data['department']}' not found"]);
                return;
            }
        }

        $existing = !empty($data['code'])
            ? Classroom::withoutGlobalScopes()->where('code', $data['code'])->first()
            : null;

        if ($existing) {
            switch ($this->duplicateHandling) {
                case self::DUPLICATE_FAIL:
                    $this->addFailedRow($rowNumber, $data, ["Code '{$data['code']}' already exists"]);
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
            $departmentId = $this->resolveDepartmentId($data);

            Classroom::create([
                'uuid' => (string) Str::uuid(),
                'department_id' => $departmentId,
                'name' => $data['name'],
                'code' => $data['code'],
                'building' => $data['building'] ?? null,
                'floor' => $data['floor'] ?? null,
                'capacity' => $data['capacity'] ?? 30,
                'type' => $this->normalizeType($data['type'] ?? 'classroom'),
                'description' => $data['description'] ?? null,
                'has_projector' => $this->parseBool($data['has_projector'] ?? false),
                'has_whiteboard' => $this->parseBool($data['has_whiteboard'] ?? true),
                'has_ac' => $this->parseBool($data['has_ac'] ?? false),
                'is_available' => true,
                'status' => $this->parseStatus($data['status'] ?? 'active'),
            ]);

            $this->importedCount++;
        } catch (\Exception $e) {
            $this->addFailedRow($rowNumber, $data, [$e->getMessage()]);
        }
    }

    protected function updateRecord(Classroom $record, array $data, int $rowNumber): void
    {
        try {
            $record->update([
                'name' => $data['name'],
                'building' => $data['building'] ?? $record->building,
                'floor' => $data['floor'] ?? $record->floor,
                'capacity' => $data['capacity'] ?? $record->capacity,
                'type' => $this->normalizeType($data['type'] ?? $record->type),
                'description' => $data['description'] ?? $record->description,
                'has_projector' => isset($data['has_projector']) ? $this->parseBool($data['has_projector']) : $record->has_projector,
                'has_whiteboard' => isset($data['has_whiteboard']) ? $this->parseBool($data['has_whiteboard']) : $record->has_whiteboard,
                'has_ac' => isset($data['has_ac']) ? $this->parseBool($data['has_ac']) : $record->has_ac,
                'status' => $this->parseStatus($data['status'] ?? 'active'),
            ]);

            $this->updatedCount++;
        } catch (\Exception $e) {
            $this->addFailedRow($rowNumber, $data, [$e->getMessage()]);
        }
    }

    protected function resolveDepartmentId(array $data): ?int
    {
        if (!empty($data['department'])) {
            $dept = Department::withoutGlobalScopes()->where('name', $data['department'])->first();
            return $dept?->id;
        }
        return null;
    }

    protected function normalizeType(?string $value): string
    {
        if (empty($value)) return 'classroom';
        $value = strtolower(str_replace(' ', '_', trim($value)));
        return in_array($value, ['lecture_hall', 'classroom', 'lab', 'seminar', 'auditorium', 'workshop'])
            ? $value : 'classroom';
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

    protected function parseBool($value): bool
    {
        if (is_bool($value)) return $value;
        $value = strtolower(trim((string) $value));
        return in_array($value, ['1', 'true', 'yes']);
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
