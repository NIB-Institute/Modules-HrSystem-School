<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Program;

class UpdateProgramAction
{
    public function execute(Program $program, array $data): Program
    {
        $program->update($data);

        return $program->fresh();
    }
}
