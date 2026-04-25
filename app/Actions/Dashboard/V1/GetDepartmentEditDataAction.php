<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\Employee\Models\Location;
use Modules\School\Http\Resources\Dashboard\V1\DepartmentResource;
use Modules\School\Models\Department;
use Modules\School\Models\School;

class GetDepartmentEditDataAction
{
    public function execute(Department $department): array
    {
        // Load relationships including location
        $department->load(['school', 'location']);

        $schools = School::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn ($school) => [
                'value' => (string) $school->id,
                'label' => $school->name . ($school->code ? " ({$school->code})" : ''),
            ]);

        // Get available locations - include current department's location + unlinked locations
        $locations = Location::where('is_active', true)
            ->where(function ($query) use ($department) {
                $query->whereNull('locationable_type')
                    ->orWhere('locationable_type', '!=', 'Modules\School\Models\Department')
                    // Include current department's linked location
                    ->orWhere(function ($q) use ($department) {
                        $q->where('locationable_type', 'Modules\School\Models\Department')
                            ->where('locationable_id', $department->id);
                    });
            })
            ->orderBy('name')
            ->get(['id', 'uuid', 'name', 'code', 'type', 'city', 'geofence_type', 'geofence_radius'])
            ->map(fn ($location) => [
                'value' => (string) $location->id,
                'label' => $location->name . ($location->code ? " ({$location->code})" : ''),
                'type' => $location->type,
                'city' => $location->city,
                'geofence_type' => $location->geofence_type,
                'geofence_radius' => $location->geofence_radius,
            ]);

        return [
            'department' => (new DepartmentResource($department))->resolve(),
            'schools' => $schools,
            'availableLocations' => $locations,
        ];
    }
}
