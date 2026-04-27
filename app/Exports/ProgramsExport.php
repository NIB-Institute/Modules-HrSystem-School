<?php

namespace Modules\School\Exports;

use App\Concerns\Exports\HasSelectableColumns;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\School\Models\Program;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProgramsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use HasSelectableColumns;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(protected array $filters = [])
    {
    }

    /**
     * @return array<string, array{label: string, value: callable, default?: bool}>
     */
    public function columnMap(): array
    {
        return [
            'id' => ['label' => 'ID', 'value' => fn ($p) => $p->id, 'default' => false],
            'name' => ['label' => 'Name', 'value' => fn ($p) => $p->name],
            'code' => ['label' => 'Code', 'value' => fn ($p) => $p->code],
            'school' => ['label' => 'School', 'value' => fn ($p) => $p->school?->name ?? ''],
            'department' => ['label' => 'Department', 'value' => fn ($p) => $p->department?->name ?? ''],
            'degree_level' => ['label' => 'Degree Level', 'value' => fn ($p) => ucfirst(str_replace('_', ' ', (string) $p->degree_level))],
            'duration_years' => ['label' => 'Duration Years', 'value' => fn ($p) => $p->duration_years],
            'credits_required' => ['label' => 'Credits Required', 'value' => fn ($p) => $p->credits_required],
            'tuition_fee' => ['label' => 'Tuition Fee', 'value' => fn ($p) => $p->tuition_fee],
            'max_students' => ['label' => 'Max Students', 'value' => fn ($p) => $p->max_students],
            'current_enrollment' => ['label' => 'Current Enrollment', 'value' => fn ($p) => $p->current_enrollment],
            'admission_requirements' => ['label' => 'Admission Requirements', 'value' => fn ($p) => $p->admission_requirements, 'default' => false],
            'accreditation_status' => ['label' => 'Accreditation Status', 'value' => fn ($p) => $p->accreditation_status, 'default' => false],
            'description' => ['label' => 'Description', 'value' => fn ($p) => $p->description, 'default' => false],
            'status' => ['label' => 'Status', 'value' => fn ($p) => $p->status ? 'Active' : 'Inactive'],
            'created_at' => ['label' => 'Created At', 'value' => fn ($p) => $p->created_at?->format('Y-m-d H:i:s'), 'default' => false],
        ];
    }

    public function query(): Builder
    {
        if ($this->isTemplateMode()) {
            return Program::query()->whereRaw('1 = 0');
        }

        $query = Program::query()->with(['school', 'department']);

        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (! empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $status = filter_var($this->filters['status'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($status !== null) {
                $query->where('status', $status);
            }
        }

        if (! empty($this->filters['school_id'])) {
            $query->where('school_id', $this->filters['school_id']);
        }

        if (! empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }

        if (! empty($this->filters['degree_level'])) {
            $query->where('degree_level', $this->filters['degree_level']);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return $this->selectedHeadings();
    }

    public function map($row): array
    {
        return $this->selectedRow($row);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
