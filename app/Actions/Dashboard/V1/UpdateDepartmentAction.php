<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\Employee\Models\Location;
use Modules\School\Models\Department;

class UpdateDepartmentAction
{
    public function execute(Department $department, array $data): Department
    {
        // Extract location_id before updating department
        $locationId = $data['location_id'] ?? null;
        $unlinkLocation = array_key_exists('location_id', $data) && $locationId === null;
        unset($data['location_id']);

        $department->update($data);

        // Handle Location linking/unlinking
        $currentLocation = $department->location;

        if ($unlinkLocation && $currentLocation) {
            // Unlink current location
            $currentLocation->update([
                'locationable_type' => null,
                'locationable_id' => null,
            ]);
        } elseif ($locationId) {
            // Unlink old location if different
            if ($currentLocation && $currentLocation->id != $locationId) {
                $currentLocation->update([
                    'locationable_type' => null,
                    'locationable_id' => null,
                ]);
            }

            // Link new location
            Location::where('id', $locationId)->update([
                'locationable_type' => Department::class,
                'locationable_id' => $department->id,
            ]);
        }

        return $department->fresh(['location']);
    }
}
