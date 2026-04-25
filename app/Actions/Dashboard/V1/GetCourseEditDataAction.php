<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\CourseResource;
use Modules\School\Models\Course;
use Modules\School\Models\Department;
use Modules\School\Models\Program;

class GetCourseEditDataAction
{
    public function execute(Course $course): array
    {
        $course->load(['department', 'program', 'instructor']);

        $departments = Department::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $programs = Program::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return [
            'course' => (new CourseResource($course))->resolve(),
            'types' => Course::getCourseTypes(),
            'departments' => $departments,
            'programs' => $programs,
        ];
    }
}
