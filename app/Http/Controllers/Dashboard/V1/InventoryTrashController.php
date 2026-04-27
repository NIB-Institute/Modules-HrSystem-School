<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\TrashController;
use Modules\School\Models\Inventory;

class InventoryTrashController extends TrashController
{
    protected function getModelClass(): string
    {
        return Inventory::class;
    }

    protected function getTrashPagePath(): string
    {
        return 'school::Dashboard/V1/Inventory/Trash';
    }

    protected function getRoutePrefix(): string
    {
        return 'school.inventories.trash';
    }

    protected function getEntityLabel(): string
    {
        return 'Inventory item';
    }

    protected function getEntityLabelPlural(): string
    {
        return 'Inventory';
    }
}
