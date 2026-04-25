<?php

namespace Modules\School\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\School\Models\School;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:schools,code'],
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(School::getTypes()))],
            'description' => ['nullable', 'string', 'max:2000'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'string'],
            'established_year' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'accreditation' => ['nullable', 'string', 'max:255'],
            'school_lavel' => ['nullable', 'string', 'max:100'],
            'currency' => ['nullable', 'integer'],
            'education_system' => ['nullable', 'string', 'max:100'],
            'tuition_fee_base' => ['nullable', 'numeric', 'min:0'],
            'total_students' => ['nullable', 'integer', 'min:0'],
            'total_staff' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'School name is required.',
            'name.max' => 'School name must be less than 255 characters.',
            'code.unique' => 'This school code is already in use.',
            'type.required' => 'School type is required.',
            'type.in' => 'Invalid school type selected.',
            'email.email' => 'Please enter a valid email address.',
            'website.url' => 'Please enter a valid website URL.',
            'established_year.min' => 'Established year must be at least 1800.',
            'established_year.max' => 'Established year cannot be in the future.',
        ];
    }
}
