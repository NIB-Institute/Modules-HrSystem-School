<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Equipment;

class DeleteEquipmentAction
{
    public function execute(Equipment $equipment): bool
    {
        return $equipment->delete();
    }
}
