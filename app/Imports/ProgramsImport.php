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
use Modules\School\Models\Program;
use Modules\School\Models\Department;
use Modules\School\Models\School;
use App\Services\TenantService;

class ProgramsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithBatchInserts, WithChunkReading
{
    public const DUPLICATE_SKIP = 'skip';
    public const DUPLICATE_UPDATE = 'update';
    public const DUPLICATE_FAIL = 'fail';

    protected array $errors = [];
    protected array $failedRows = [];
    protected int $importedCount = 0;
    protected int $updatedCount = 0;
    protected int $skippedCount = 0;
    protected ?int $defaultSchoolId;
    protected string $duplicateHandling;
    protected bool $previewMode = false;
    protected array $previewData = [];

    public function __construct(string $duplicateHandling = self::DUPLICATE_SKIP, bool $previewMode = false)
    {
        $tenantService = app(TenantService::class);
        $this->defaultSchoolId = $tenantService->hasTenantType('School')
            ? $tenantService->getTenantIds('School')[0] ?? null
            : null;
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

            // Resolve school name for preview
            $schoolName = $data['school'] ?? null;
            if ($schoolName) {
                $school = School::withoutGlobalScopes()->where('name', $schoolName)->first();
                if (!$school) {
                    $schoolName = $schoolName . ' (not found)';
                }
            } elseif ($this->defaultSchoolId) {
                $defaultSchool = School::withoutGlobalScopes()->find($this->defaultSchoolId);
                $schoolName = $defaultSchool ? $defaultSchool->name . ' (default)' : null;
            }

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
                'school' => $schoolName,
                'department' => $departmentName,
                'degree_level' => $data['degree_level'] ?? null,
                'duration_years' => $data['duration_years'] ?? null,
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

            // Validate school if specified
            if (!empty($data['school'])) {
                $school = School::withoutGlobalScopes()->where('name', $data['school'])->first();
                if (!$school) {
                    $preview['status'] = 'error';
                    $preview['errors'][] = "School '{$data['school']}' not found. Please check the school name matches exactly.";
                }
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
                $existing = Program::withoutGlobalScopes()->where('code', $data['code'])->first();
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

        // Validate school if specified
        if (!empty($data['school'])) {
            $school = School::withoutGlobalScopes()->where('name', $data['school'])->first();
            if (!$school) {
                $this->addFailedRow($rowNumber, $data, ["School '{$data['school']}' not found"]);
                return;
            }
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
            ? Program::withoutGlobalScopes()->where('code', $data['code'])->first()
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
            $schoolId = $this->resolveSchoolId($data);
            $departmentId = $this->resolveDepartmentId($data);

            Program::create([
                'uuid' => (string) Str::uuid(),
                'school_id' => $schoolId,
                'department_id' => $departmentId,
                'name' => $data['name'],
                'code' => $data['code'],
                'description' => $data['description'] ?? null,
                'degree_level' => $this->normalizeDegreeLevel($data['degree_level'] ?? 'bachelor'),
                'duration_years' => $data['duration_years'] ?? null,
                'credits_required' => $data['credits_required'] ?? null,
                'tuition_fee' => $data['tuition_fee'] ?? null,
                'max_students' => $data['max_students'] ?? null,
                'admission_requirements' => $data['admission_requirements'] ?? null,
                'status' => $this->parseStatus($data['status'] ?? 'active'),
            ]);

            $this->importedCount++;
        } catch (\Exception $e) {
            $this->addFailedRow($rowNumber, $data, [$e->getMessage()]);
        }
    }

    protected function updateRecord(Program $record, array $data, int $rowNumber): void
    {
        try {
            $record->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? $record->description,
                'degree_level' => $this->normalizeDegreeLevel($data['degree_level'] ?? $record->degree_level),
                'duration_years' => $data['duration_years'] ?? $record->duration_years,
                'credits_required' => $data['credits_required'] ?? $record->credits_required,
                'tuition_fee' => $data['tuition_fee'] ?? $record->tuition_fee,
                'max_students' => $data['max_students'] ?? $record->max_students,
                'admission_requirements' => $data['admission_requirements'] ?? $record->admission_requirements,
                'status' => $this->parseStatus($data['status'] ?? 'active'),
            ]);

            $this->updatedCount++;
        } catch (\Exception $e) {
            $this->addFailedRow($rowNumber, $data, [$e->getMessage()]);
        }
    }

    protected function resolveSchoolId(array $data): ?int
    {
        // First, check if a school name is provided in the Excel data
        if (!empty($data['school'])) {
            $school = School::withoutGlobalScopes()
                ->where('name', $data['school'])
                ->first();

            if ($school) {
                return $school->id;
            }
        }

        // Fall back to default school if no school specified or not found
        if ($this->defaultSchoolId) {
            return $this->defaultSchoolId;
        }

        $tenantService = app(TenantService::class);
        if (!$tenantService->hasTenant()) {
            $firstSchool = School::withoutGlobalScopes()->first();
            return $firstSchool?->id;
        }

        return null;
    }

    protected function resolveDepartmentId(array $data): ?int
    {
        if (!empty($data['department'])) {
            $dept = Department::withoutGlobalScopes()->where('name', $data['department'])->first();
            return $dept?->id;
        }
        return null;
    }

    protected function normalizeDegreeLevel(?string $value): string
    {
        if (empty($value)) return 'bachelor';
        $value = strtolower(trim($value));
        return in_array($value, ['certificate', 'diploma', 'associate', 'bachelor', 'master', 'doctorate'])
            ? $value : 'bachelor';
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
