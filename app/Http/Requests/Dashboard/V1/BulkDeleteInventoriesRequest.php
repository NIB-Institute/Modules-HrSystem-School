<?php

namespace Modules\School\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteInventoriesRequest extends FormRequest
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
            'uuids.*' => ['required', 'string', 'uuid', 'exists:school_inventories,uuid'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'uuids.required' => 'Please select at least one inventory item to delete.',
            'uuids.min' => 'Please select at least one inventory item to delete.',
            'uuids.*.exists' => 'One or more selected inventory items do not exist.',
        ];
    }
}
