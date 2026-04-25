<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\School;

class GetSchoolCreateDataAction
{
    public function execute(): array
    {
        return [
            'types' => School::getTypes(),
        ];
    }
}
