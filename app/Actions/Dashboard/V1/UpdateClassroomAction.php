<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Classroom;

class UpdateClassroomAction
{
    public function execute(Classroom $classroom, array $data): Classroom
    {
        $equipmentIds = $data['equipment_ids'] ?? [];
        unset($data['equipment_ids']);

        $classroom->update($data);

        $classroom->equipmentItems()->sync($equipmentIds);

        return $classroom->fresh();
    }
}
