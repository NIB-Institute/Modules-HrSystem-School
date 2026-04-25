<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\ProgramResource;
use Modules\School\Models\Department;
use Modules\School\Models\Program;
use Modules\School\Models\School;

class GetProgramEditDataAction
{
    public function execute(Program $program): array
    {
        $program->load(['school', 'department']);

        return [
            'program' => (new ProgramResource($program))->resolve(),
            'degreeLevels' => Program::getDegreeLevels(),
            'schools' => School::select('id', 'name')
                ->where('status', true)
                ->orderBy('name')
                ->get()
                ->toArray(),
            'departments' => Department::select('id', 'name', 'school_id')
                ->where('status', true)
                ->orderBy('name')
                ->get()
                ->toArray(),
        ];
    }
}
