<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Course;

class ToggleCourseStatusAction
{
    public function execute(Course $course): Course
    {
        $course->status = !$course->status;
        $course->save();

        return $course;
    }
}
