<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\TrashController;
use Modules\School\Models\Program;

class ProgramTrashController extends TrashController
{
    protected function getModelClass(): string
    {
        return Program::class;
    }

    protected function getTrashPagePath(): string
    {
        return 'school::Dashboard/V1/Program/Trash';
    }

    protected function getRoutePrefix(): string
    {
        return 'school.programs.trash';
    }

    protected function getEntityLabel(): string
    {
        return 'Program';
    }

    protected function getEntityLabelPlural(): string
    {
        return 'Programs';
    }
}
