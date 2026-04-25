<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\TrashController;
use Modules\School\Models\School;

class SchoolTrashController extends TrashController
{
    protected function getModelClass(): string
    {
        return School::class;
    }

    protected function getTrashPagePath(): string
    {
        return 'school::Dashboard/V1/School/Trash';
    }

    protected function getRoutePrefix(): string
    {
        return 'school.schools.trash';
    }

    protected function getEntityLabel(): string
    {
        return 'School';
    }

    protected function getEntityLabelPlural(): string
    {
        return 'Schools';
    }
}
