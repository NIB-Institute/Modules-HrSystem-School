<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Inventory;

class BulkDeleteInventoriesAction
{
    /**
     * Execute bulk delete for inventories.
     *
     * @param  array<string>  $uuids  Array of inventory UUIDs to delete
     * @return array{deleted: int, failed: int}
     */
    public function execute(array $uuids): array
    {
        $deleted = 0;
        $failed = 0;

        DB::transaction(function () use ($uuids, &$deleted, &$failed) {
            foreach ($uuids as $uuid) {
                $inventory = Inventory::where('uuid', $uuid)->first();

                if ($inventory) {
                    $inventory->delete();
                    $deleted++;
                } else {
                    $failed++;
                }
            }
        });

        return [
            'deleted' => $deleted,
            'failed' => $failed,
        ];
    }
}
