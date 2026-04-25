<?php

namespace Modules\School\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Modules\School\Models\Program;

class ProgramService
{
    /**
     * Get paginated programs with filters.
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Program::with(['school', 'department'])->withCount('courses');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['degree_level']) && $filters['degree_level'] !== 'all') {
            $query->where('degree_level', $filters['degree_level']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new program.
     */
    public function create(array $data): Program
    {
        $data['uuid'] = (string) Str::uuid();
        return Program::create($data);
    }

    /**
     * Update a program.
     */
    public function update(Program $program, array $data): Program
    {
        $program->update($data);
        return $program->fresh();
    }

    /**
     * Delete a program.
     */
    public function delete(Program $program): bool
    {
        return $program->delete();
    }

    /**
     * Get program statistics.
     */
    public function getStats(): array
    {
        return [
            'total' => Program::count(),
            'active' => Program::where('status', true)->count(),
            'inactive' => Program::where('status', false)->count(),
            'bachelor' => Program::where('degree_level', 'bachelor')->count(),
            'master' => Program::where('degree_level', 'master')->count(),
            'doctorate' => Program::where('degree_level', 'doctorate')->count(),
        ];
    }

    /**
     * Toggle program status.
     */
    public function toggleStatus(Program $program): Program
    {
        $program->status = !$program->status;
        $program->save();
        return $program;
    }
}
