<?php

namespace Modules\School\Http\Resources\Dashboard\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\School\Models\School;

class SchoolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'logo' => $this->logo,
            'established_year' => $this->established_year,
            'accreditation' => $this->accreditation,
            'total_students' => $this->total_students,
            'total_staff' => $this->total_staff,
            'status' => $this->status,
            'departments_count' => $this->whenCounted('departments'),
            'programs_count' => $this->whenCounted('programs'),
            'employees_count' => $this->whenCounted('employees'),
            'courses_count' => $this->whenCounted('courses'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    protected function getTypeLabel(): string
    {
        $types = School::getTypes();
        return $types[$this->type] ?? ucfirst($this->type);
    }
}
