<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\DepartmentResource;
use Modules\School\Http\Resources\Dashboard\V1\ProgramResource;
use Modules\School\Models\Department;

class GetDepartmentShowDataAction
{
    public function execute(Department $department): array
    {
        $department->load(['school', 'programs', 'courses', 'employees']);
        $department->loadCount(['programs', 'courses', 'employees']);

        $programs = $department->programs()
            ->withCount('courses')
            ->orderBy('name')
            ->get();

        return [
            'department' => (new DepartmentResource($department))->resolve(),
            'programs' => ProgramResource::collection($programs)->resolve(),
            'stats' => [
                'programs_count' => $department->programs_count,
                'courses_count' => $department->courses_count,
                'employees_count' => $department->employees_count,
            ],
        ];
    }
}
