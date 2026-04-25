<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Str;
use Modules\Employee\Models\Location;
use Modules\School\Models\Department;

class CreateDepartmentAction
{
    public function execute(array $data): Department
    {
        // Extract location_id before creating department
        $locationId = $data['location_id'] ?? null;
        unset($data['location_id']);

        $data['uuid'] = (string) Str::uuid();

        $department = Department::create($data);

        // Link Location to Department if provided
        if ($locationId) {
            Location::where('id', $locationId)->update([
                'locationable_type' => Department::class,
                'locationable_id' => $department->id,
            ]);
        }

        return $department;
    }
}
