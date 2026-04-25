<?php

namespace Modules\School\Http\Resources\Dashboard\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\School\Models\Course;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'department_id' => $this->department_id,
            'program_id' => $this->program_id,
            'instructor_id' => $this->instructor_id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'credits' => $this->credits,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'semester' => $this->semester,
            'year' => $this->year,
            'max_students' => $this->max_students,
            'current_enrollment' => $this->current_enrollment,
            'available_seats' => $this->max_students ? $this->max_students - $this->current_enrollment : null,
            'schedule' => $this->schedule,
            'room' => $this->room,
            'prerequisites' => $this->prerequisites,
            'syllabus' => $this->syllabus,
            'status' => $this->status,
            'department_name' => $this->whenLoaded('department', fn() => $this->department?->name),
            'program_name' => $this->whenLoaded('program', fn() => $this->program?->name),
            'instructor_name' => $this->whenLoaded('instructor', fn() => $this->instructor?->full_name),
            'has_available_seats' => $this->hasAvailableSeats(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    protected function getTypeLabel(): string
    {
        $types = Course::getCourseTypes();
        return $types[$this->type] ?? ucfirst($this->type);
    }
}
