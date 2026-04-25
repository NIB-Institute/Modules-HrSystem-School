<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Classroom;

class ToggleClassroomStatusAction
{
    public function execute(Classroom $classroom): Classroom
    {
        $classroom->status = !$classroom->status;
        $classroom->save();

        return $classroom;
    }
}
