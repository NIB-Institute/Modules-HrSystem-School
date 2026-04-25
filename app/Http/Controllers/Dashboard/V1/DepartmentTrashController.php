<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\TrashController;
use Modules\School\Models\Department;

class DepartmentTrashController extends TrashController
{
    protected function getModelClass(): string
    {
        return Department::class;
    }

    protected function getTrashPagePath(): string
    {
        return 'school::Dashboard/V1/Department/Trash';
    }

    protected function getRoutePrefix(): string
    {
        return 'school.departments.trash';
    }

    protected function getEntityLabel(): string
    {
        return 'Department';
    }

    protected function getEntityLabelPlural(): string
    {
        return 'Departments';
    }
}
