<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Str;
use Modules\School\Models\Program;

class CreateProgramAction
{
    public function execute(array $data): Program
    {
        $data['uuid'] = (string) Str::uuid();

        return Program::create($data);
    }
}
