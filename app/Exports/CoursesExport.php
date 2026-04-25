<?php

namespace Modules\School\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\School\Models\Course;

class CoursesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Course::query()->with(['department', 'program', 'instructor', 'classroom']);

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

        if (!empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }

        if (!empty($this->filters['program_id'])) {
            $query->where('program_id', $this->filters['program_id']);
        }

        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'Code', 'Department', 'Program', 'Instructor', 'Classroom',
            'Credits', 'Type', 'Semester', 'Year', 'Max Students', 'Current Enrollment',
            'Schedule', 'Room', 'Description', 'Status', 'Created At'
        ];
    }

    public function map($course): array
    {
        return [
            $course->id,
            $course->name,
            $course->code,
            $course->department?->name ?? '',
            $course->program?->name ?? '',
            $course->instructor?->full_name ?? '',
            $course->classroom?->name ?? '',
            $course->credits,
            ucfirst($course->type),
            $course->semester,
            $course->year,
            $course->max_students,
            $course->current_enrollment,
            $course->schedule,
            $course->room,
            $course->description,
            $course->status ? 'Active' : 'Inactive',
            $course->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
