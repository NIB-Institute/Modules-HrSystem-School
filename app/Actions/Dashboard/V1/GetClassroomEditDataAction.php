<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\ClassroomResource;
use Modules\School\Models\Classroom;
use Modules\School\Models\Department;
use Modules\School\Models\Equipment;

class GetClassroomEditDataAction
{
    public function execute(Classroom $classroom): array
    {
        $classroom->load('equipmentItems');

        $departments = Department::with('school')
            ->where('status', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($department) => [
                'id' => $department->id,
                'name' => $department->school
                    ? "{$department->name} ({$department->school->name})"
                    : $department->name,
            ])
            ->toArray();

        $equipmentOptions = Equipment::where('status', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(fn ($equipment) => [
                'value' => $equipment->id,
                'label' => $equipment->name,
                'category' => $equipment->category,
            ])
            ->toArray();

        return [
            'classroom' => (new ClassroomResource($classroom))->resolve(),
            'types' => Classroom::getClassroomTypes(),
            'departments' => $departments,
            'equipmentOptions' => $equipmentOptions,
        ];
    }
}
