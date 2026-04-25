<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\CourseResource;
use Modules\School\Models\Course;
use Modules\School\Models\Department;
use Modules\School\Models\Program;

class GetCourseIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = Course::with(['department', 'program', 'instructor']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['department_id']) && $filters['department_id'] !== 'all') {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['program_id']) && $filters['program_id'] !== 'all') {
            $query->where('program_id', $filters['program_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status'] === '1' || $filters['status'] === 'active');
        }

        $courses = $query->latest()->paginate($perPage);

        $stats = [
            'total' => Course::count(),
            'active' => Course::where('status', true)->count(),
            'inactive' => Course::where('status', false)->count(),
            'required' => Course::where('type', Course::TYPE_REQUIRED)->count(),
            'elective' => Course::where('type', Course::TYPE_ELECTIVE)->count(),
            'core' => Course::where('type', Course::TYPE_CORE)->count(),
        ];

        $departments = Department::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $programs = Program::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return [
            'courses' => [
                'data' => CourseResource::collection($courses)->resolve(),
                'meta' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => $stats,
            'types' => Course::getCourseTypes(),
            'departments' => $departments,
            'programs' => $programs,
        ];
    }
}
