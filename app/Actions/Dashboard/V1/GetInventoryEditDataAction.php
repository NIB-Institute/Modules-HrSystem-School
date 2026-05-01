<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\InventoryResource;
use Modules\School\Models\Classroom;
use Modules\School\Models\Department;
use Modules\School\Models\Equipment;
use Modules\School\Models\Inventory;

class GetInventoryEditDataAction
{
    public function execute(Inventory $inventory): array
    {
        return [
            'inventory'     => (new InventoryResource($inventory))->resolve(),
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
