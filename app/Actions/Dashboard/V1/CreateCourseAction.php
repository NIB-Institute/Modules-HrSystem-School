<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Str;
use Modules\School\Models\Course;

class CreateCourseAction
{
    public function execute(array $data): Course
    {
        $data['uuid'] = (string) Str::uuid();

        return Course::create($data);
    }
}
