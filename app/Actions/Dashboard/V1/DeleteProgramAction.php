<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Program;

class DeleteProgramAction
{
    public function execute(Program $program): bool
    {
        return $program->delete();
    }
}
