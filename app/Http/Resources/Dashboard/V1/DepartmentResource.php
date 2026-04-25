<?php

namespace Modules\School\Http\Resources\Dashboard\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'school_id' => $this->school_id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'head_of_department' => $this->head_of_department,
            'email' => $this->email,
            'phone' => $this->phone,
            'office_location' => $this->office_location,
            'established_year' => $this->established_year,
            'staff_count' => $this->staff_count,
            'total_staff' => $this->total_staff,
            'total_students' => $this->total_students,
            'status' => $this->status,

            // Linked Location (preferred geofence source)
            'location' => $this->whenLoaded('location', fn() => $this->location ? [
                'id' => $this->location->id,
                'uuid' => $this->location->uuid,
                'name' => $this->location->name,
                'code' => $this->location->code,
                'type' => $this->location->type,
                'latitude' => (float) $this->location->latitude,
                'longitude' => (float) $this->location->longitude,
                'geofence_radius' => $this->location->geofence_radius,
                'geofence_type' => $this->location->geofence_type ?? 'circle',
                'polygon_coordinates' => $this->location->polygon_coordinates,
                'enforce_geofence' => (bool) $this->location->enforce_geofence,
                'timezone' => $this->location->timezone,
                'is_active' => (bool) $this->location->is_active,
                'google_maps_url' => $this->location->google_maps_url,
            ] : null),
            'location_id' => $this->location?->id,

            // Geofence fields (fallback, prefer location relationship)
            'latitude' => $this->location ? (float) $this->location->latitude : $this->latitude,
            'longitude' => $this->location ? (float) $this->location->longitude : $this->longitude,
            'geofence_radius' => $this->location ? $this->location->geofence_radius : ($this->geofence_radius ?? 100),
            'enforce_geofence' => $this->location ? (bool) $this->location->enforce_geofence : (bool) $this->enforce_geofence,
            'timezone' => $this->location ? $this->location->timezone : $this->timezone,
            'has_geofence' => $this->hasGeofence(),
            'google_maps_url' => $this->getGoogleMapsUrl(),

            'school_name' => $this->whenLoaded('school', fn() => $this->school?->name),
            'school' => $this->whenLoaded('school', fn() => [
                'id' => $this->school->id,
                'uuid' => $this->school->uuid,
                'name' => $this->school->name,
                'code' => $this->school->code,
            ]),
            'programs_count' => $this->whenCounted('programs'),
            'courses_count' => $this->whenCounted('courses'),
            'employees_count' => $this->whenCounted('employees'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
