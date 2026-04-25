<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Department;

class DeleteDepartmentAction
{
    public function execute(Department $department): bool
    {
        return $department->delete();
    }
}
