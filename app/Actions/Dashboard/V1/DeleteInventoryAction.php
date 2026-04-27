<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Inventory;

class DeleteInventoryAction
{
    public function execute(Inventory $inventory): bool
    {
        return $inventory->delete();
    }
}
