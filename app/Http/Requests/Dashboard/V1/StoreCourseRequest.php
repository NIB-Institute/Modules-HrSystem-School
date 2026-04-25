<?php

namespace Modules\School\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\School\Models\Course;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['nullable', 'integer', 'exists:school_departments,id'],
            'program_id' => ['nullable', 'integer', 'exists:school_programs,id'],
            'instructor_id' => ['nullable', 'integer', 'exists:employees,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:school_courses,code'],
            'description' => ['nullable', 'string', 'max:2000'],
            'credits' => ['required', 'integer', 'min:1', 'max:20'],
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(Course::getCourseTypes()))],
            'semester' => ['nullable', 'integer', 'min:1', 'max:8'],
            'year' => ['nullable', 'integer', 'min:1', 'max:10'],
            'max_students' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'current_enrollment' => ['nullable', 'integer', 'min:0'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:100'],
            'prerequisites' => ['nullable', 'array'],
            'prerequisites.*' => ['string'],
            'syllabus' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Course name is required.',
            'name.max' => 'Course name must be less than 255 characters.',
            'code.unique' => 'This course code is already in use.',
            'credits.required' => 'Credits are required.',
            'credits.min' => 'Credits must be at least 1.',
            'credits.max' => 'Credits cannot exceed 20.',
            'type.required' => 'Course type is required.',
            'type.in' => 'Invalid course type selected.',
            'department_id.exists' => 'The selected department does not exist.',
            'program_id.exists' => 'The selected program does not exist.',
            'instructor_id.exists' => 'The selected instructor does not exist.',
            'max_students.min' => 'Maximum students must be at least 1.',
            'max_students.max' => 'Maximum students cannot exceed 1000.',
        ];
    }
}
