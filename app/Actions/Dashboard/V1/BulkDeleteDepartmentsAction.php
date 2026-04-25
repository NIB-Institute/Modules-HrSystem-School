<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Department;

class BulkDeleteDepartmentsAction
{
    /**
     * Execute bulk delete for departments.
     *
     * @param array<string> $uuids Array of department UUIDs to delete
     * @return array{deleted: int, failed: int}
     */
    public function execute(array $uuids): array
    {
        $deleted = 0;
        $failed = 0;

        DB::transaction(function () use ($uuids, &$deleted, &$failed) {
            foreach ($uuids as $uuid) {
                $department = Department::where('uuid', $uuid)->first();

                if ($department) {
                    $department->delete();
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
