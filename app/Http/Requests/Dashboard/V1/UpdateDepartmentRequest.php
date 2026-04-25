<?php

namespace Modules\School\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['required', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('school_departments', 'code')->ignore($this->route('department'))],
            'description' => ['nullable', 'string', 'max:2000'],
            'head_of_department' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'office_location' => ['nullable', 'string', 'max:255'],
            'established_year' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'total_staff' => ['nullable', 'integer', 'min:0'],
            'total_students' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],

            // Location linking (preferred geofence source)
            'location_id' => ['nullable', 'exists:employee_locations,id'],

            // Geofence fields (fallback if no location linked)
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'geofence_radius' => ['nullable', 'integer', 'min:10', 'max:5000'],
            'enforce_geofence' => ['boolean'],
            'timezone' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'school_id.required' => 'Please select a school.',
            'school_id.exists' => 'The selected school does not exist.',
            'name.required' => 'Department name is required.',
            'name.max' => 'Department name must be less than 255 characters.',
            'code.unique' => 'This department code is already in use.',
            'email.email' => 'Please enter a valid email address.',
            'established_year.min' => 'Established year must be at least 1800.',
            'established_year.max' => 'Established year cannot be in the future.',

            // Geofence validation messages
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'geofence_radius.min' => 'Geofence radius must be at least 10 meters.',
            'geofence_radius.max' => 'Geofence radius cannot exceed 5000 meters.',
            'timezone.max' => 'Timezone must be less than 50 characters.',
        ];
    }
}
