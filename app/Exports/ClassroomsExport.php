<?php

namespace Modules\School\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\School\Models\Classroom;

class ClassroomsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Classroom::query()->with('department');

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

        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'Code', 'Department', 'Building', 'Floor', 'Capacity', 'Type',
            'Has Projector', 'Has Whiteboard', 'Has AC', 'Is Available', 'Description', 'Status', 'Created At'
        ];
    }

    public function map($classroom): array
    {
        return [
            $classroom->id,
            $classroom->name,
            $classroom->code,
            $classroom->department?->name ?? '',
            $classroom->building,
            $classroom->floor,
            $classroom->capacity,
            ucfirst(str_replace('_', ' ', $classroom->type)),
            $classroom->has_projector ? 'Yes' : 'No',
            $classroom->has_whiteboard ? 'Yes' : 'No',
            $classroom->has_ac ? 'Yes' : 'No',
            $classroom->is_available ? 'Yes' : 'No',
            $classroom->description,
            $classroom->status ? 'Active' : 'Inactive',
            $classroom->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
