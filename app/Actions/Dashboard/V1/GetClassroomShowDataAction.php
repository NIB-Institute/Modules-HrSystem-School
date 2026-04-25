<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\ClassroomResource;
use Modules\School\Models\Classroom;

class GetClassroomShowDataAction
{
    public function execute(Classroom $classroom): array
    {
        $classroom->load(['department.school', 'courses', 'equipmentItems']);
        $classroom->loadCount(['courses', 'equipmentItems']);

        return [
            'classroom' => (new ClassroomResource($classroom))->resolve(),
            'stats' => [
                'courses_count' => $classroom->courses_count,
                'equipment_count' => $classroom->equipment_items_count,
                'capacity' => $classroom->capacity,
            ],
        ];
    }
}
