<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Course;
use Modules\School\Models\Department;
use Modules\School\Models\Program;

class GetCourseCreateDataAction
{
    public function execute(): array
    {
        $departments = Department::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $programs = Program::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return [
            'types' => Course::getCourseTypes(),
            'departments' => $departments,
            'programs' => $programs,
        ];
    }
}
