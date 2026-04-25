<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\DepartmentResource;
use Modules\School\Models\Department;
use Modules\School\Models\School;

class GetDepartmentIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = Department::with('school')
            ->withCount(['programs', 'courses', 'employees']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('head_of_department', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['school_id']) && $filters['school_id'] !== 'all') {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status'] === '1' || $filters['status'] === 'active');
        }

        $departments = $query->latest()->paginate($perPage);

        $stats = [
            'total' => Department::count(),
            'active' => Department::where('status', true)->count(),
            'inactive' => Department::where('status', false)->count(),
        ];

        $schools = School::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn ($school) => [
                'value' => (string) $school->id,
                'label' => $school->name . ($school->code ? " ({$school->code})" : ''),
            ]);

        return [
            'departments' => [
                'data' => DepartmentResource::collection($departments)->resolve(),
                'meta' => [
                    'current_page' => $departments->currentPage(),
                    'last_page' => $departments->lastPage(),
                    'per_page' => $departments->perPage(),
                    'total' => $departments->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => $stats,
            'schools' => $schools,
        ];
    }
}
