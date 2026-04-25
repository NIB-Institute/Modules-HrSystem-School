<?php

namespace Modules\School\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Modules\School\Models\School;

class SchoolService
{
    /**
     * Get paginated schools with filters.
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
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

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new school.
     */
    public function create(array $data): School
    {
        $data['uuid'] = (string) Str::uuid();
        return School::create($data);
    }

    /**
     * Update a school.
     */
    public function update(School $school, array $data): School
    {
        $school->update($data);
        return $school->fresh();
    }

    /**
     * Delete a school.
     */
    public function delete(School $school): bool
    {
        return $school->delete();
    }

    /**
     * Get school statistics.
     */
    public function getStats(): array
    {
        return [
            'total' => School::count(),
            'active' => School::where('status', true)->count(),
            'inactive' => School::where('status', false)->count(),
            'universities' => School::where('type', 'university')->count(),
            'institutes' => School::where('type', 'institute')->count(),
            'colleges' => School::where('type', 'college')->count(),
        ];
    }

    /**
     * Update school status.
     */
    public function updateStatus(School $school, bool $status): School
    {
        $school->status = $status;
        $school->save();
        return $school;
    }

    /**
     * Find school by UUID.
     */
    public function findByUuid(string $uuid): ?School
    {
        return School::where('uuid', $uuid)->first();
    }
}
