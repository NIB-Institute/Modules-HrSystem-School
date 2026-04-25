<?php

namespace Modules\School\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\School\Models\Classroom;

class UpdateClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['required', 'exists:school_departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('school_classrooms', 'code')->ignore($this->route('classroom'))],
            'building' => ['nullable', 'string', 'max:100'],
            'floor' => ['nullable', 'integer', 'min:0', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10000'],
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(Classroom::getClassroomTypes()))],
            'equipment' => ['nullable', 'array'],
            'equipment.*' => ['string'],
            'equipment_ids' => ['nullable', 'array'],
            'equipment_ids.*' => ['integer', 'exists:school_equipment,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'has_projector' => ['required', 'boolean'],
            'has_whiteboard' => ['required', 'boolean'],
            'has_ac' => ['required', 'boolean'],
            'is_available' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'The selected department is invalid.',
            'name.required' => 'Classroom name is required.',
            'name.max' => 'Classroom name must be less than 255 characters.',
            'code.unique' => 'This classroom code is already in use.',
            'type.required' => 'Classroom type is required.',
            'type.in' => 'Invalid classroom type selected.',
            'capacity.required' => 'Capacity is required.',
            'capacity.min' => 'Capacity must be at least 1.',
            'capacity.max' => 'Capacity cannot exceed 10000.',
        ];
    }
}
