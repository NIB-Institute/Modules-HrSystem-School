<?php

namespace Modules\School\Exports;

use App\Concerns\Exports\HasSelectableColumns;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\School\Models\Classroom;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClassroomsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'building' => ['label' => 'Building', 'value' => fn ($c) => $c->building],
            'floor' => ['label' => 'Floor', 'value' => fn ($c) => $c->floor],
            'capacity' => ['label' => 'Capacity', 'value' => fn ($c) => $c->capacity],
            'type' => ['label' => 'Type', 'value' => fn ($c) => ucfirst(str_replace('_', ' ', (string) $c->type))],
            'has_projector' => ['label' => 'Has Projector', 'value' => fn ($c) => $c->has_projector ? 'Yes' : 'No', 'default' => false],
            'has_whiteboard' => ['label' => 'Has Whiteboard', 'value' => fn ($c) => $c->has_whiteboard ? 'Yes' : 'No', 'default' => false],
            'has_ac' => ['label' => 'Has AC', 'value' => fn ($c) => $c->has_ac ? 'Yes' : 'No', 'default' => false],
            'is_available' => ['label' => 'Is Available', 'value' => fn ($c) => $c->is_available ? 'Yes' : 'No'],
            'description' => ['label' => 'Description', 'value' => fn ($c) => $c->description, 'default' => false],
            'status' => ['label' => 'Status', 'value' => fn ($c) => $c->status ? 'Active' : 'Inactive'],
            'created_at' => ['label' => 'Created At', 'value' => fn ($c) => $c->created_at?->format('Y-m-d H:i:s'), 'default' => false],
        ];
    }

    public function query(): Builder
    {
        if ($this->isTemplateMode()) {
            return Classroom::query()->whereRaw('1 = 0');
        }

        $query = Classroom::query()->with('department');

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
