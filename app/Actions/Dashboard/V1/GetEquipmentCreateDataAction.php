<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Equipment;

class GetEquipmentCreateDataAction
{
    public function execute(): array
    {
        return [
            'categories' => Equipment::getCategories(),
        ];
    }
}
