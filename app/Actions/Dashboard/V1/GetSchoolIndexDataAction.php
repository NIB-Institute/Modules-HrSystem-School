<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\SchoolResource;
use Modules\School\Models\School;

class GetSchoolIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = School::withCount(['departments', 'programs', 'employees']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status'] === '1' || $filters['status'] === 'active');
        }

        $schools = $query->latest()->paginate($perPage);

        $stats = [
            'total' => School::count(),
            'active' => School::where('status', true)->count(),
            'inactive' => School::where('status', false)->count(),
            'universities' => School::where('type', 'university')->count(),
            'institutes' => School::where('type', 'institute')->count(),
            'colleges' => School::where('type', 'college')->count(),
        ];

        return [
            'schools' => [
                'data' => SchoolResource::collection($schools)->resolve(),
                'meta' => [
                    'current_page' => $schools->currentPage(),
                    'last_page' => $schools->lastPage(),
                    'per_page' => $schools->perPage(),
                    'total' => $schools->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => $stats,
            'types' => School::getTypes(),
        ];
    }
}
