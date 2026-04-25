<?php

namespace Modules\School\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\School\Models\Program;

class StoreProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'department_id' => ['nullable', 'integer', 'exists:school_departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:school_programs,code'],
            'description' => ['nullable', 'string', 'max:2000'],
            'degree_level' => ['required', 'string', 'in:' . implode(',', array_keys(Program::getDegreeLevels()))],
            'duration_years' => ['nullable', 'integer', 'min:1', 'max:10'],
            'credits_required' => ['nullable', 'integer', 'min:0', 'max:500'],
            'tuition_fee' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'admission_requirements' => ['nullable', 'string', 'max:2000'],
            'program_coordinator' => ['nullable', 'string', 'max:255'],
            'max_students' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'current_enrollment' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'accreditation_status' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'school_id.required' => 'Please select a school.',
            'school_id.exists' => 'The selected school does not exist.',
            'name.required' => 'Program name is required.',
            'name.max' => 'Program name must be less than 255 characters.',
            'code.unique' => 'This program code is already in use.',
            'degree_level.required' => 'Please select a degree level.',
            'degree_level.in' => 'Invalid degree level selected.',
            'duration_years.min' => 'Duration must be at least 1 year.',
            'duration_years.max' => 'Duration cannot exceed 10 years.',
            'credits_required.min' => 'Credits required cannot be negative.',
            'tuition_fee.min' => 'Tuition fee cannot be negative.',
            'max_students.min' => 'Maximum students cannot be negative.',
            'current_enrollment.min' => 'Current enrollment cannot be negative.',
        ];
    }
}
