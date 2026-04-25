<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\School;

class UpdateSchoolAction
{
    public function execute(School $school, array $data): School
    {
        $school->update($data);

        return $school->fresh();
    }
}
