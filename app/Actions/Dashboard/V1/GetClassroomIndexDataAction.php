<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\ClassroomResource;
use Modules\School\Models\Classroom;
use Modules\School\Models\Department;

class GetClassroomIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = Classroom::with(['department.school'])->withCount(['courses']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('building', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status'] === '1' || $filters['status'] === 'active');
        }

        if (!empty($filters['department_id']) && $filters['department_id'] !== 'all') {
            $query->where('department_id', $filters['department_id']);
        }

        $classrooms = $query->latest()->paginate($perPage);

        $stats = [
            'total' => Classroom::count(),
            'active' => Classroom::where('status', true)->count(),
            'available' => Classroom::where('is_available', true)->where('status', true)->count(),
            'by_type' => [
                'lecture_hall' => Classroom::where('type', 'lecture_hall')->count(),
                'classroom' => Classroom::where('type', 'classroom')->count(),
                'lab' => Classroom::where('type', 'lab')->count(),
                'seminar' => Classroom::where('type', 'seminar')->count(),
                'auditorium' => Classroom::where('type', 'auditorium')->count(),
                'workshop' => Classroom::where('type', 'workshop')->count(),
            ],
        ];

        $departments = Department::with('school')
            ->where('status', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($department) => [
                'id' => $department->id,
                'name' => $department->school
                    ? "{$department->name} ({$department->school->name})"
                    : $department->name,
            ])
            ->toArray();

        return [
            'classrooms' => [
                'data' => ClassroomResource::collection($classrooms)->resolve(),
                'meta' => [
                    'current_page' => $classrooms->currentPage(),
                    'last_page' => $classrooms->lastPage(),
                    'per_page' => $classrooms->perPage(),
                    'total' => $classrooms->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => $stats,
            'types' => Classroom::getClassroomTypes(),
            'departments' => $departments,
        ];
    }
}
