<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Department;
use Modules\School\Models\Program;
use Modules\School\Models\School;

class GetProgramCreateDataAction
{
    public function execute(): array
    {
        return [
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
