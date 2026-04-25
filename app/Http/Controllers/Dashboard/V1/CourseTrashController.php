<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\TrashController;
use Modules\School\Models\Course;

class CourseTrashController extends TrashController
{
    protected function getModelClass(): string
    {
        return Course::class;
    }

    protected function getTrashPagePath(): string
    {
        return 'school::Dashboard/V1/Course/Trash';
    }

    protected function getRoutePrefix(): string
    {
        return 'school.courses.trash';
    }

    protected function getEntityLabel(): string
    {
        return 'Course';
    }

    protected function getEntityLabelPlural(): string
    {
        return 'Courses';
    }
}
