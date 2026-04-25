<?php

namespace Modules\School\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'uuids' => ['required', 'array', 'min:1'],
            'uuids.*' => ['required', 'string', 'uuid', 'exists:school_equipment,uuid'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'uuids.required' => 'Please select at least one equipment item to delete.',
            'uuids.min' => 'Please select at least one equipment item to delete.',
            'uuids.*.exists' => 'One or more selected equipment items do not exist.',
        ];
    }
}
