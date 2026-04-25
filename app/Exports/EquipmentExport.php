<?php

namespace Modules\School\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\School\Models\Equipment;

class EquipmentExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Equipment::query();

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $status = filter_var($this->filters['status'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($status !== null) {
                $query->where('status', $status);
            }
        }

        if (!empty($this->filters['category'])) {
            $query->where('category', $this->filters['category']);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Slug', 'Category', 'Icon', 'Description', 'Status', 'Created At'];
    }

    public function map($equipment): array
    {
        return [
            $equipment->id,
            $equipment->name,
            $equipment->slug,
            ucfirst($equipment->category),
            $equipment->icon,
            $equipment->description,
            $equipment->status ? 'Active' : 'Inactive',
            $equipment->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
