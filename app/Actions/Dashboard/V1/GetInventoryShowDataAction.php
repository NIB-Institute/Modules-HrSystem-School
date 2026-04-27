<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\InventoryResource;
use Modules\School\Models\Inventory;

class GetInventoryShowDataAction
{
    public function execute(Inventory $inventory): array
    {
        $inventory->load(['equipment', 'classroom', 'department', 'assignedTo']);

        return [
            'inventory' => (new InventoryResource($inventory))->resolve(),
        ];
    }
}
