<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\EquipmentResource;
use Modules\School\Models\Equipment;

class GetEquipmentEditDataAction
{
    public function execute(Equipment $equipment): array
    {
        return [
            'equipment' => (new EquipmentResource($equipment))->resolve(),
            'categories' => Equipment::getCategories(),
        ];
    }
}
