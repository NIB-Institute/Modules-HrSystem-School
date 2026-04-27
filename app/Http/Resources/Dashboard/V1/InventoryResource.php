<?php

namespace Modules\School\Http\Resources\Dashboard\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\School\Models\Inventory;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'asset_tag' => $this->asset_tag,
            'serial_number' => $this->serial_number,
            'name' => $this->name,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'condition' => $this->condition,
            'condition_label' => $this->getConditionLabel(),
            'cost' => $this->cost,
            'vendor' => $this->vendor,
            'purchased_at' => $this->purchased_at?->toDateString(),
            'warranty_until' => $this->warranty_until?->toDateString(),
            'warranty_expired' => $this->isExpiredWarranty(),
            'notes' => $this->notes,
            'is_active' => $this->is_active,

            'equipment_id' => $this->equipment_id,
            'classroom_id' => $this->classroom_id,
            'department_id' => $this->department_id,
            'assigned_to_user_id' => $this->assigned_to_user_id,

            'equipment' => $this->whenLoaded('equipment', fn () => [
                'id' => $this->equipment->id,
                'name' => $this->equipment->name,
            ]),
            'classroom' => $this->whenLoaded('classroom', fn () => $this->classroom ? [
                'id' => $this->classroom->id,
                'name' => $this->classroom->name,
            ] : null),
            'department' => $this->whenLoaded('department', fn () => $this->department ? [
                'id' => $this->department->id,
                'name' => $this->department->name,
            ] : null),
            'assigned_to' => $this->whenLoaded('assignedTo', fn () => $this->assignedTo ? [
                'id' => $this->assignedTo->id,
                'name' => $this->assignedTo->name,
                'email' => $this->assignedTo->email,
            ] : null),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    protected function getStatusLabel(): string
    {
        return Inventory::statuses()[$this->status] ?? ucfirst((string) $this->status);
    }

    protected function getConditionLabel(): string
    {
        return Inventory::conditions()[$this->condition] ?? ucfirst((string) $this->condition);
    }
}
