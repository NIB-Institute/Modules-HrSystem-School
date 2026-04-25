<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Classroom;

class DeleteClassroomAction
{
    public function execute(Classroom $classroom): bool
    {
        return $classroom->delete();
    }
}
