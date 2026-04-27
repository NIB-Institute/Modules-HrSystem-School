<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Classroom;
use Modules\School\Models\Department;
use Modules\School\Models\Equipment;
use Modules\School\Models\Inventory;

class GetInventoryCreateDataAction
{
    public function execute(): array
    {
        return [
            'statuses' => Inventory::statuses(),
            'conditions' => Inventory::conditions(),
            'equipment' => Equipment::select('id', 'name')->orderBy('name')->get(),
            'classrooms' => Classroom::select('id', 'name')->orderBy('name')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name')->get(),
        ];
    }
}
