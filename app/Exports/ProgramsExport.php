<?php

namespace Modules\School\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\School\Models\Program;

class ProgramsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Program::query()->with(['school', 'department']);

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $status = filter_var($this->filters['status'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($status !== null) {
                $query->where('status', $status);
            }
        }

        if (!empty($this->filters['school_id'])) {
            $query->where('school_id', $this->filters['school_id']);
        }

        if (!empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }

        if (!empty($this->filters['degree_level'])) {
            $query->where('degree_level', $this->filters['degree_level']);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'Code', 'School', 'Department', 'Degree Level', 'Duration Years',
            'Credits Required', 'Tuition Fee', 'Max Students', 'Current Enrollment',
            'Admission Requirements', 'Accreditation Status', 'Description', 'Status', 'Created At'
        ];
    }

    public function map($program): array
    {
        return [
            $program->id,
            $program->name,
            $program->code,
            $program->school?->name ?? '',
            $program->department?->name ?? '',
            ucfirst(str_replace('_', ' ', $program->degree_level)),
            $program->duration_years,
            $program->credits_required,
            $program->tuition_fee,
            $program->max_students,
            $program->current_enrollment,
            $program->admission_requirements,
            $program->accreditation_status,
            $program->description,
            $program->status ? 'Active' : 'Inactive',
            $program->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
