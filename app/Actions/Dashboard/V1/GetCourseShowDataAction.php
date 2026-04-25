<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\CourseResource;
use Modules\School\Models\Course;

class GetCourseShowDataAction
{
    public function execute(Course $course): array
    {
        $course->load(['department', 'program', 'instructor']);

        return [
            'course' => (new CourseResource($course))->resolve(),
            'stats' => [
                'credits' => $course->credits,
                'max_students' => $course->max_students,
                'current_enrollment' => $course->current_enrollment,
                'available_seats' => $course->max_students - $course->current_enrollment,
            ],
        ];
    }
}
