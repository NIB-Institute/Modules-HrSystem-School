<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Classroom;
use Modules\School\Models\Department;
use Modules\School\Models\Equipment;

class GetClassroomCreateDataAction
{
    public function execute(): array
    {
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
            'types' => Classroom::getClassroomTypes(),
            'departments' => $departments,
            'equipmentOptions' => $equipmentOptions,
        ];
    }
}
