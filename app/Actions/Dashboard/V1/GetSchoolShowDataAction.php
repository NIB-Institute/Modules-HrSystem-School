<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\DepartmentResource;
use Modules\School\Http\Resources\Dashboard\V1\SchoolResource;
use Modules\School\Http\Resources\Dashboard\V1\ProgramResource;
use Modules\School\Models\School;

class GetSchoolShowDataAction
{
    public function execute(School $school): array
    {
        $school->load(['departments', 'programs', 'employees']);
        $school->loadCount(['departments', 'programs', 'employees', 'courses']);

        $departments = $school->departments()
            ->withCount(['programs', 'courses', 'employees'])
            ->orderBy('name')
            ->get();

        $programs = $school->programs()
            ->with('department')
            ->withCount('courses')
            ->orderBy('name')
            ->get();

        return [
            'school' => (new SchoolResource($school))->resolve(),
            'departments' => DepartmentResource::collection($departments)->resolve(),
            'programs' => ProgramResource::collection($programs)->resolve(),
            'stats' => [
                'departments_count' => $school->departments_count,
                'programs_count' => $school->programs_count,
                'employees_count' => $school->employees_count,
                'courses_count' => $school->courses_count,
            ],
        ];
    }
}
