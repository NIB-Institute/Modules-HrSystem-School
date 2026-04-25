<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Course;

class UpdateCourseAction
{
    public function execute(Course $course, array $data): Course
    {
        $course->update($data);

        return $course->fresh();
    }
}
