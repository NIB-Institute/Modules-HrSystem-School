<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Classroom;
use Modules\School\Models\Department;
use Modules\School\Models\Equipment;
use Modules\School\Models\Inventory;
use Modules\School\Services\InventoryTagGenerator;

class GetInventoryCreateDataAction
{
    public function __construct(private InventoryTagGenerator $tagGenerator) {}

    public function execute(): array
    {
        return [
            'statuses'   => Inventory::statuses(),
            'conditions' => Inventory::conditions(),
            'equipment'  => Equipment::select('id', 'name')->orderBy('name')->get(),
            'classrooms' => Classroom::select('id', 'name', 'department_id')
                ->orderBy('name')
                ->get(),
            'departments' => Department::select('id', 'name')->orderBy('name')->get(),
            'suggested_asset_tag' => $this->tagGenerator->generate(),
        ];
    }
}
