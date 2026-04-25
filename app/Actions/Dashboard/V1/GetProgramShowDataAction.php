<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\CourseResource;
use Modules\School\Http\Resources\Dashboard\V1\ProgramResource;
use Modules\School\Models\Program;

class GetProgramShowDataAction
{
    public function execute(Program $program): array
    {
        $program->load(['school', 'department', 'courses']);
        $program->loadCount('courses');

        $courses = $program->courses()
            ->orderBy('name')
            ->get();

        return [
            'program' => (new ProgramResource($program))->resolve(),
            'courses' => CourseResource::collection($courses)->resolve(),
            'stats' => [
                'courses_count' => $program->courses_count,
                'current_enrollment' => $program->current_enrollment ?? 0,
                'max_students' => $program->max_students ?? 0,
                'credits_required' => $program->credits_required ?? 0,
            ],
        ];
    }
}
