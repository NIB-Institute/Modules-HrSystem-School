<?php

namespace Modules\School\Exports;

use App\Concerns\Exports\HasSelectableColumns;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\School\Models\Course;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoursesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'id' => ['label' => 'ID', 'value' => fn ($c) => $c->id, 'default' => false],
            'name' => ['label' => 'Name', 'value' => fn ($c) => $c->name],
            'code' => ['label' => 'Code', 'value' => fn ($c) => $c->code],
            'department' => ['label' => 'Department', 'value' => fn ($c) => $c->department?->name ?? ''],
            'program' => ['label' => 'Program', 'value' => fn ($c) => $c->program?->name ?? ''],
            'instructor' => ['label' => 'Instructor', 'value' => fn ($c) => $c->instructor?->full_name ?? ''],
            'classroom' => ['label' => 'Classroom', 'value' => fn ($c) => $c->classroom?->name ?? ''],
            'credits' => ['label' => 'Credits', 'value' => fn ($c) => $c->credits],
            'type' => ['label' => 'Type', 'value' => fn ($c) => ucfirst($c->type)],
            'semester' => ['label' => 'Semester', 'value' => fn ($c) => $c->semester],
            'year' => ['label' => 'Year', 'value' => fn ($c) => $c->year],
            'max_students' => ['label' => 'Max Students', 'value' => fn ($c) => $c->max_students],
            'current_enrollment' => ['label' => 'Current Enrollment', 'value' => fn ($c) => $c->current_enrollment],
            'schedule' => ['label' => 'Schedule', 'value' => fn ($c) => $c->schedule, 'default' => false],
            'room' => ['label' => 'Room', 'value' => fn ($c) => $c->room, 'default' => false],
            'description' => ['label' => 'Description', 'value' => fn ($c) => $c->description, 'default' => false],
            'status' => ['label' => 'Status', 'value' => fn ($c) => $c->status ? 'Active' : 'Inactive'],
            'created_at' => ['label' => 'Created At', 'value' => fn ($c) => $c->created_at?->format('Y-m-d H:i:s'), 'default' => false],
        ];
    }

    public function query(): Builder
    {
        if ($this->isTemplateMode()) {
            return Course::query()->whereRaw('1 = 0');
        }

        $query = Course::query()->with(['department', 'program', 'instructor', 'classroom']);

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

        if (! empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }

        if (! empty($this->filters['program_id'])) {
            $query->where('program_id', $this->filters['program_id']);
        }

        if (! empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
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
