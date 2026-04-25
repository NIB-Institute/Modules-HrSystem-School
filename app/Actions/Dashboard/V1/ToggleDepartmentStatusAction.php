<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Department;

class ToggleDepartmentStatusAction
{
    public function execute(Department $department): Department
    {
        $department->status = !$department->status;
        $department->save();

        return $department;
    }
}
