<?php

namespace Modules\School\Exports;

use App\Concerns\Exports\HasSelectableColumns;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\School\Models\Equipment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquipmentExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'id' => ['label' => 'ID', 'value' => fn ($e) => $e->id, 'default' => false],
            'name' => ['label' => 'Name', 'value' => fn ($e) => $e->name],
            'slug' => ['label' => 'Slug', 'value' => fn ($e) => $e->slug],
            'category' => ['label' => 'Category', 'value' => fn ($e) => ucfirst((string) $e->category)],
            'icon' => ['label' => 'Icon', 'value' => fn ($e) => $e->icon, 'default' => false],
            'description' => ['label' => 'Description', 'value' => fn ($e) => $e->description, 'default' => false],
            'status' => ['label' => 'Status', 'value' => fn ($e) => $e->status ? 'Active' : 'Inactive'],
            'created_at' => ['label' => 'Created At', 'value' => fn ($e) => $e->created_at?->format('Y-m-d H:i:s'), 'default' => false],
        ];
    }

    public function query(): Builder
    {
        if ($this->isTemplateMode()) {
            return Equipment::query()->whereRaw('1 = 0');
        }

        $query = Equipment::query();

        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (! empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $status = filter_var($this->filters['status'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($status !== null) {
                $query->where('status', $status);
            }
        }

        if (! empty($this->filters['category'])) {
            $query->where('category', $this->filters['category']);
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
