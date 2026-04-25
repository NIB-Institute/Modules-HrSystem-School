<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Course;

class DeleteCourseAction
{
    public function execute(Course $course): bool
    {
        return $course->delete();
    }
}
