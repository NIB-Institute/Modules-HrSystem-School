<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Str;
use Modules\School\Models\Classroom;

class CreateClassroomAction
{
    public function execute(array $data): Classroom
    {
        $equipmentIds = $data['equipment_ids'] ?? [];
        unset($data['equipment_ids']);

        $data['uuid'] = (string) Str::uuid();

        $classroom = Classroom::create($data);

        if (!empty($equipmentIds)) {
            $classroom->equipmentItems()->sync($equipmentIds);
        }

        return $classroom;
    }
}
