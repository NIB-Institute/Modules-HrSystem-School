<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Str;
use Modules\School\Models\School;

class CreateSchoolAction
{
    public function execute(array $data): School
    {
        $data['uuid'] = (string) Str::uuid();

        return School::create($data);
    }
}
