<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Program;

class ToggleProgramStatusAction
{
    public function execute(Program $program): Program
    {
        $program->status = !$program->status;
        $program->save();

        return $program;
    }
}
