<?php

namespace Modules\School\Http\Resources\Dashboard\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\School\Models\Program;

class ProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'school_id' => $this->school_id,
            'department_id' => $this->department_id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'degree_level' => $this->degree_level,
            'degree_level_label' => $this->getDegreeLevelLabel(),
            'duration_years' => $this->duration_years,
            'credits_required' => $this->credits_required,
            'tuition_fee' => $this->tuition_fee,
            'admission_requirements' => $this->admission_requirements,
            'program_coordinator' => $this->program_coordinator,
            'max_students' => $this->max_students,
            'current_enrollment' => $this->current_enrollment,
            'accreditation_status' => $this->accreditation_status,
            'status' => $this->status,
            'school_name' => $this->whenLoaded('school', fn() => $this->school?->name),
            'department_name' => $this->whenLoaded('department', fn() => $this->department?->name),
            'courses_count' => $this->whenCounted('courses'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    protected function getDegreeLevelLabel(): string
    {
        $levels = Program::getDegreeLevels();
        return $levels[$this->degree_level] ?? ucfirst($this->degree_level ?? '');
    }
}
