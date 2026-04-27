<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Inventory;

class CreateInventoryAction
{
    public function execute(array $data): Inventory
    {
        return Inventory::create($data);
    }
}
