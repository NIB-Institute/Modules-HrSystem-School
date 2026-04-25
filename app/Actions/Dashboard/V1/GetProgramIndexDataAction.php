<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\ProgramResource;
use Modules\School\Models\Department;
use Modules\School\Models\Program;
use Modules\School\Models\School;

class GetProgramIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = Program::with(['school', 'department'])
            ->withCount('courses');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('program_coordinator', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['degree_level']) && $filters['degree_level'] !== 'all') {
            $query->where('degree_level', $filters['degree_level']);
        }

        if (!empty($filters['school_id']) && $filters['school_id'] !== 'all') {
            $query->where('school_id', $filters['school_id']);
        }

        if (!empty($filters['department_id']) && $filters['department_id'] !== 'all') {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status'] === '1' || $filters['status'] === 'active');
        }

        $programs = $query->latest()->paginate($perPage);

        $stats = [
            'total' => Program::count(),
            'active' => Program::where('status', true)->count(),
            'inactive' => Program::where('status', false)->count(),
            'bachelor' => Program::where('degree_level', 'bachelor')->count(),
            'master' => Program::where('degree_level', 'master')->count(),
            'doctorate' => Program::where('degree_level', 'doctorate')->count(),
        ];

        return [
            'programs' => [
                'data' => ProgramResource::collection($programs)->resolve(),
                'meta' => [
                    'current_page' => $programs->currentPage(),
                    'last_page' => $programs->lastPage(),
                    'per_page' => $programs->perPage(),
                    'total' => $programs->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => $stats,
            'degreeLevels' => Program::getDegreeLevels(),
            'schools' => School::select('id', 'name')->orderBy('name')->get()->toArray(),
            'departments' => Department::select('id', 'name', 'school_id')->orderBy('name')->get()->toArray(),
        ];
    }
}
