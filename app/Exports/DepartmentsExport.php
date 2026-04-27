<?php

namespace Modules\School\Exports;

use App\Concerns\Exports\HasSelectableColumns;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\School\Models\Department;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartmentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'id' => ['label' => 'ID', 'value' => fn ($d) => $d->id, 'default' => false],
            'name' => ['label' => 'Name', 'value' => fn ($d) => $d->name],
            'code' => ['label' => 'Code', 'value' => fn ($d) => $d->code],
            'school' => ['label' => 'School', 'value' => fn ($d) => $d->school?->name ?? ''],
            'description' => ['label' => 'Description', 'value' => fn ($d) => $d->description, 'default' => false],
            'email' => ['label' => 'Email', 'value' => fn ($d) => $d->email],
            'phone' => ['label' => 'Phone', 'value' => fn ($d) => $d->phone],
            'office_location' => ['label' => 'Office Location', 'value' => fn ($d) => $d->office_location, 'default' => false],
            'established_year' => ['label' => 'Established Year', 'value' => fn ($d) => $d->established_year, 'default' => false],
            'total_staff' => ['label' => 'Total Staff', 'value' => fn ($d) => $d->total_staff],
            'total_students' => ['label' => 'Total Students', 'value' => fn ($d) => $d->total_students],
            'status' => ['label' => 'Status', 'value' => fn ($d) => $d->status ? 'Active' : 'Inactive'],
            'created_at' => ['label' => 'Created At', 'value' => fn ($d) => $d->created_at?->format('Y-m-d H:i:s'), 'default' => false],
        ];
    }

    public function query(): Builder
    {
        if ($this->isTemplateMode()) {
            return Department::query()->whereRaw('1 = 0');
        }

        $query = Department::query()->with('school');

        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (! empty($this->filters['school_id']) && $this->filters['school_id'] !== 'all') {
            $query->where('school_id', $this->filters['school_id']);
        }

        if (isset($this->filters['status']) && $this->filters['status'] !== 'all') {
            $status = filter_var($this->filters['status'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($status !== null) {
                $query->where('status', $status);
            }
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
