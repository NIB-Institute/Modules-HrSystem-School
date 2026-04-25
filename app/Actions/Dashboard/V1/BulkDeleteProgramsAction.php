<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Program;

class BulkDeleteProgramsAction
{
    /**
     * Execute bulk delete for programs.
     *
     * @param array<string> $uuids Array of program UUIDs to delete
     * @return array{deleted: int, failed: int}
     */
    public function execute(array $uuids): array
    {
        $deleted = 0;
        $failed = 0;

        DB::transaction(function () use ($uuids, &$deleted, &$failed) {
            foreach ($uuids as $uuid) {
                $program = Program::where('uuid', $uuid)->first();

                if ($program) {
                    $program->delete();
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
