<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\InventoryResource;
use Modules\School\Models\Classroom;
use Modules\School\Models\Department;
use Modules\School\Models\Equipment;
use Modules\School\Models\Inventory;

class GetInventoryIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = Inventory::with(['equipment', 'classroom', 'department', 'assignedTo']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['equipment_id']) && $filters['equipment_id'] !== 'all') {
            $query->where('equipment_id', $filters['equipment_id']);
        }

        if (! empty($filters['classroom_id']) && $filters['classroom_id'] !== 'all') {
            $query->where('classroom_id', $filters['classroom_id']);
        }

        if (! empty($filters['department_id']) && $filters['department_id'] !== 'all') {
            $query->where('department_id', $filters['department_id']);
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['condition']) && $filters['condition'] !== 'all') {
            $query->where('condition', $filters['condition']);
        }

        $inventories = $query->latest()->paginate($perPage);

        $stats = [
            'total'     => Inventory::count(),
            'in_stock'  => Inventory::where('status', Inventory::STATUS_IN_STOCK)->count(),
            'in_use'    => Inventory::where('status', Inventory::STATUS_IN_USE)->count(),
            'maintenance' => Inventory::where('status', Inventory::STATUS_MAINTENANCE)->count(),
            'retired'   => Inventory::where('status', Inventory::STATUS_RETIRED)->count(),
            'lost'      => Inventory::where('status', Inventory::STATUS_LOST)->count(),
        ];

        return [
            'inventories' => [
                'data' => InventoryResource::collection($inventories)->resolve(),
                'meta' => [
                    'current_page' => $inventories->currentPage(),
                    'last_page'    => $inventories->lastPage(),
                    'per_page'     => $inventories->perPage(),
                    'total'        => $inventories->total(),
                ],
            ],
            'filters' => $filters,
            'stats'         => $stats,
            'statuses'      => Inventory::statuses(),
            'conditions'    => Inventory::conditions(),
            'equipment'     => Equipment::select('id', 'name')->orderBy('name')->get(),
            'classrooms'    => Classroom::select('id', 'name', 'department_id')
                ->orderBy('name')
                ->get(),
            'departments' => Department::select('id', 'name')->orderBy('name')->get(),
        ];
    }
}
