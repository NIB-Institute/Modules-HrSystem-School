<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\Employee\Models\Location;
use Modules\School\Models\School;

class GetDepartmentCreateDataAction
{
    public function execute(): array
    {
        $schools = School::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn ($school) => [
                'value' => (string) $school->id,
                'label' => $school->name . ($school->code ? " ({$school->code})" : ''),
            ]);

        // Get available locations that can be linked to departments
        // Only show locations not already linked to a department
        $locations = Location::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('locationable_type')
                    ->orWhere('locationable_type', '!=', 'Modules\School\Models\Department');
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
            'schools' => $schools,
            'availableLocations' => $locations,
        ];
    }
}
