<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Equipment;

class BulkDeleteEquipmentAction
{
    /**
     * Execute bulk delete for equipment.
     *
     * @param array<string> $uuids Array of equipment UUIDs to delete
     * @return array{deleted: int, failed: int}
     */
    public function execute(array $uuids): array
    {
        $deleted = 0;
        $failed = 0;

        DB::transaction(function () use ($uuids, &$deleted, &$failed) {
            foreach ($uuids as $uuid) {
                $equipment = Equipment::where('uuid', $uuid)->first();

                if ($equipment) {
                    $equipment->delete();
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
