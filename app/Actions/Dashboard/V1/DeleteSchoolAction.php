<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\School;

class DeleteSchoolAction
{
    public function execute(School $school): bool
    {
        return $school->delete();
    }
}
