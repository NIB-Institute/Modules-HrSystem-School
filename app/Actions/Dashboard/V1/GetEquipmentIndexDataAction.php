<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\EquipmentResource;
use Modules\School\Models\Equipment;

class GetEquipmentIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = Equipment::withCount(['classrooms']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status'] === '1' || $filters['status'] === 'active');
        }

        $equipment = $query->latest()->paginate($perPage);

        $stats = [
            'total' => Equipment::count(),
            'active' => Equipment::where('status', true)->count(),
            'by_category' => [
                'technology' => Equipment::where('category', 'technology')->count(),
                'furniture' => Equipment::where('category', 'furniture')->count(),
                'safety' => Equipment::where('category', 'safety')->count(),
                'accessibility' => Equipment::where('category', 'accessibility')->count(),
                'other' => Equipment::where('category', 'other')->count(),
            ],
        ];

        return [
            'equipment' => [
                'data' => EquipmentResource::collection($equipment)->resolve(),
                'meta' => [
                    'current_page' => $equipment->currentPage(),
                    'last_page' => $equipment->lastPage(),
                    'per_page' => $equipment->perPage(),
                    'total' => $equipment->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => $stats,
            'categories' => Equipment::getCategories(),
        ];
    }
}
