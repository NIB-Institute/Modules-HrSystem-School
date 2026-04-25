<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Equipment;

class UpdateEquipmentAction
{
    public function execute(Equipment $equipment, array $data): Equipment
    {
        $equipment->update($data);

        return $equipment->fresh();
    }
}
