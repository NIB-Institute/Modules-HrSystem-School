<?php

namespace Modules\School\Models;

use App\Traits\BelongsToDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;

class Course extends Model
{
    use HasFactory, SoftDeletes, BelongsToDepartment;

    protected $table = 'school_courses';

    /**
     * Course types.
     */
    public const TYPE_REQUIRED = 'required';
    public const TYPE_ELECTIVE = 'elective';
    public const TYPE_CORE = 'core';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'department_id',
        'program_id',
        'instructor_id',
        'classroom_id',
        'name',
        'code',
        'description',
        'credits',
        'type',
        'semester',
        'year',
        'max_students',
        'current_enrollment',
        'schedule',
        'room',
        'prerequisites',
        'syllabus',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'credits' => 'integer',
        'semester' => 'integer',
        'year' => 'integer',
        'max_students' => 'integer',
        'current_enrollment' => 'integer',
        'prerequisites' => 'array',
        'status' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the department that owns the course.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the program that owns the course.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the instructor for the course.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'instructor_id');
    }

    /**
     * Get the classroom for the course.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Get available course types.
     */
    public static function getCourseTypes(): array
    {
        return [
            self::TYPE_REQUIRED => 'Required',
            self::TYPE_ELECTIVE => 'Elective',
            self::TYPE_CORE => 'Core',
        ];
    }

    /**
     * Scope for active courses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Check if course has available seats.
     */
    public function hasAvailableSeats(): bool
    {
        return $this->current_enrollment < $this->max_students;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
