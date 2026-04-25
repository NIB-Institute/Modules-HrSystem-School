<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Equipment;

class CreateEquipmentAction
{
    public function execute(array $data): Equipment
    {
        return Equipment::create($data);
    }
}
