<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Inventory;

class UpdateInventoryAction
{
    public function execute(Inventory $inventory, array $data): Inventory
    {
        $inventory->update($data);

        return $inventory->fresh();
    }
}
