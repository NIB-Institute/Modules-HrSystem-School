<?php

namespace Modules\School\Exports;

use App\Concerns\Exports\HasSelectableColumns;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\School\Models\Inventory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoriesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'id' => ['label' => 'ID', 'value' => fn ($i) => $i->id, 'default' => false],
            'asset_tag' => ['label' => 'Asset Tag', 'value' => fn ($i) => $i->asset_tag],
            'serial_number' => ['label' => 'Serial Number', 'value' => fn ($i) => $i->serial_number],
            'name' => ['label' => 'Name', 'value' => fn ($i) => $i->name],
            'equipment' => ['label' => 'Equipment', 'value' => fn ($i) => $i->equipment?->name ?? ''],
            'classroom' => ['label' => 'Classroom', 'value' => fn ($i) => $i->classroom?->name ?? ''],
            'department' => ['label' => 'Department', 'value' => fn ($i) => $i->department?->name ?? ''],
            'assigned_to' => ['label' => 'Assigned To', 'value' => fn ($i) => $i->assignedTo?->name ?? ''],
            'status' => [
                'label' => 'Status',
                'value' => fn ($i) => Inventory::statuses()[$i->status] ?? $i->status,
            ],
            'condition' => [
                'label' => 'Condition',
                'value' => fn ($i) => Inventory::conditions()[$i->condition] ?? $i->condition,
            ],
            'cost' => ['label' => 'Cost', 'value' => fn ($i) => $i->cost],
            'vendor' => ['label' => 'Vendor', 'value' => fn ($i) => $i->vendor, 'default' => false],
            'purchased_at' => ['label' => 'Purchased At', 'value' => fn ($i) => $i->purchased_at?->format('Y-m-d')],
            'warranty_until' => ['label' => 'Warranty Until', 'value' => fn ($i) => $i->warranty_until?->format('Y-m-d'), 'default' => false],
            'notes' => ['label' => 'Notes', 'value' => fn ($i) => $i->notes, 'default' => false],
            'is_active' => ['label' => 'Active', 'value' => fn ($i) => $i->is_active ? 'Yes' : 'No'],
            'created_at' => ['label' => 'Created At', 'value' => fn ($i) => $i->created_at?->format('Y-m-d H:i:s'), 'default' => false],
        ];
    }

    public function query(): Builder
    {
        if ($this->isTemplateMode()) {
            return Inventory::query()->whereRaw('1 = 0');
        }

        $query = Inventory::query()->with(['equipment', 'classroom', 'department', 'assignedTo']);

        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if (! empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        if (! empty($this->filters['condition']) && $this->filters['condition'] !== 'all') {
            $query->where('condition', $this->filters['condition']);
        }

        if (! empty($this->filters['equipment_id']) && $this->filters['equipment_id'] !== 'all') {
            $query->where('equipment_id', $this->filters['equipment_id']);
        }

        if (! empty($this->filters['classroom_id']) && $this->filters['classroom_id'] !== 'all') {
            $query->where('classroom_id', $this->filters['classroom_id']);
        }

        if (! empty($this->filters['department_id']) && $this->filters['department_id'] !== 'all') {
            $query->where('department_id', $this->filters['department_id']);
        }

        return $query->latest();
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
