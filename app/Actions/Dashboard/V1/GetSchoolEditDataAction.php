<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\SchoolResource;
use Modules\School\Models\School;

class GetSchoolEditDataAction
{
    public function execute(School $school): array
    {
        return [
            'school' => (new SchoolResource($school))->resolve(),
            'types' => School::getTypes(),
        ];
    }
}
