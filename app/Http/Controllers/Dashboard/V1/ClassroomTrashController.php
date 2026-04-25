<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\TrashController;
use Modules\School\Models\Classroom;

class ClassroomTrashController extends TrashController
{
    protected function getModelClass(): string
    {
        return Classroom::class;
    }

    protected function getTrashPagePath(): string
    {
        return 'school::Dashboard/V1/Classroom/Trash';
    }

    protected function getRoutePrefix(): string
    {
        return 'school.classrooms.trash';
    }

    protected function getEntityLabel(): string
    {
        return 'Classroom';
    }

    protected function getEntityLabelPlural(): string
    {
        return 'Classrooms';
    }
}
