<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\School;

class ToggleSchoolStatusAction
{
    public function execute(School $school): School
    {
        $school->status = !$school->status;
        $school->save();

        return $school;
    }
}
