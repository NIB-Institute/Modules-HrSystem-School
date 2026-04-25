<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\TrashController;
use Modules\School\Models\Equipment;

class EquipmentTrashController extends TrashController
{
    protected function getModelClass(): string
    {
        return Equipment::class;
    }

    protected function getTrashPagePath(): string
    {
        return 'school::Dashboard/V1/Equipment/Trash';
    }

    protected function getRoutePrefix(): string
    {
        return 'school.equipment.trash';
    }

    protected function getEntityLabel(): string
    {
        return 'Equipment';
    }

    protected function getEntityLabelPlural(): string
    {
        return 'Equipment';
    }
}
