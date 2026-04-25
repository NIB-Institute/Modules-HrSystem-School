<?php

namespace Modules\School\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $equipmentId = $this->route('equipment')?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('school_equipment', 'slug')->ignore($equipmentId),
            ],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', Rule::in(['technology', 'furniture', 'safety', 'accessibility', 'other'])],
            'qty' => ['nullable', 'integer', 'min:0'],
            'price_total' => ['nullable', 'numeric', 'min:0'],
            'status' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Equipment name is required.',
            'name.max' => 'Equipment name cannot exceed 100 characters.',
            'category.required' => 'Category is required.',
            'category.in' => 'Invalid category selected.',
        ];
    }
}
