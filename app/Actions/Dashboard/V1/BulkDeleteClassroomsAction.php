<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Classroom;

class BulkDeleteClassroomsAction
{
    /**
     * Execute bulk delete for classrooms.
     *
     * @param array<string> $uuids Array of classroom UUIDs to delete
     * @return array{deleted: int, failed: int}
     */
    public function execute(array $uuids): array
    {
        $deleted = 0;
        $failed = 0;

        DB::transaction(function () use ($uuids, &$deleted, &$failed) {
            foreach ($uuids as $uuid) {
                $classroom = Classroom::where('uuid', $uuid)->first();

                if ($classroom) {
                    $classroom->delete();
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
