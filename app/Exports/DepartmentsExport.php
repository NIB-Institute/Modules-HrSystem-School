<?php

namespace Modules\School\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\School\Models\Department;

class DepartmentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Department::query()->with('school');

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

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'Code', 'School', 'Description', 'Email', 'Phone',
            'Office Location', 'Established Year', 'Total Staff', 'Total Students', 'Status', 'Created At'
        ];
    }

    public function map($department): array
    {
        return [
            $department->id,
            $department->name,
            $department->code,
            $department->school?->name ?? '',
            $department->description,
            $department->email,
            $department->phone,
            $department->office_location,
            $department->established_year,
            $department->total_staff,
            $department->total_students,
            $department->status ? 'Active' : 'Inactive',
            $department->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
