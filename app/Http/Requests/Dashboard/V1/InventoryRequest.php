<?php

namespace Modules\School\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\School\Models\Inventory;

class InventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $inventoryId = $this->route('inventory')?->id;

        return [
            'asset_tag' => [
                'required',
                'string',
                'max:50',
                Rule::unique('school_inventories', 'asset_tag')->ignore($inventoryId)->whereNull('deleted_at'),
            ],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:150'],
            'equipment_id' => ['required', 'integer', 'exists:school_equipment,id'],
            'classroom_id' => ['nullable', 'integer', 'exists:school_classrooms,id'],
            'department_id' => ['nullable', 'integer', 'exists:school_departments,id'],
            'assigned_to_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['required', Rule::in(array_keys(Inventory::statuses()))],
            'condition' => ['required', Rule::in(array_keys(Inventory::conditions()))],
            'purchased_at' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'vendor' => ['nullable', 'string', 'max:150'],
            'warranty_until' => ['nullable', 'date', 'after_or_equal:purchased_at'],
            'notes' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['string', 'max:1024'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_tag.required' => 'Asset tag is required.',
            'asset_tag.unique' => 'This asset tag is already in use.',
            'equipment_id.required' => 'You must pick the equipment type.',
            'equipment_id.exists' => 'Selected equipment does not exist.',
            'status.required' => 'Status is required.',
            'condition.required' => 'Condition is required.',
            'warranty_until.after_or_equal' => 'Warranty end date cannot be before the purchase date.',
        ];
    }
}
