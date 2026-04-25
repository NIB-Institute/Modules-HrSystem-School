<?php

namespace Modules\School\Models;

use App\Traits\BelongsToDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Classroom extends Model
{
    use HasFactory, SoftDeletes, BelongsToDepartment;

    protected $table = 'school_classrooms';

    /**
     * Classroom types.
     */
    public const TYPE_LECTURE_HALL = 'lecture_hall';
    public const TYPE_CLASSROOM = 'classroom';
    public const TYPE_LAB = 'lab';
    public const TYPE_SEMINAR = 'seminar';
    public const TYPE_AUDITORIUM = 'auditorium';
    public const TYPE_WORKSHOP = 'workshop';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'department_id',
        'name',
        'code',
        'building',
        'floor',
        'capacity',
        'type',
        'equipment',
        'description',
        'has_projector',
        'has_whiteboard',
        'has_ac',
        'is_available',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'floor' => 'integer',
        'capacity' => 'integer',
        'equipment' => 'array',
        'has_projector' => 'boolean',
        'has_whiteboard' => 'boolean',
        'has_ac' => 'boolean',
        'is_available' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'capacity' => 30,
        'type' => self::TYPE_CLASSROOM,
        'has_projector' => false,
        'has_whiteboard' => true,
        'has_ac' => false,
        'is_available' => true,
        'status' => true,
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
     * Get the department that owns the classroom.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the school through department.
     */
    public function school(): ?School
    {
        return $this->department?->school;
    }

    /**
     * Get the courses held in this classroom.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the equipment items in this classroom.
     */
    public function equipmentItems(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'school_classroom_equipment')
            ->withPivot(['value', 'quantity', 'notes'])
            ->withTimestamps();
    }

    /**
     * Get available classroom types.
     */
    public static function getClassroomTypes(): array
    {
        return [
            self::TYPE_LECTURE_HALL => 'Lecture Hall',
            self::TYPE_CLASSROOM => 'Classroom',
            self::TYPE_LAB => 'Lab',
            self::TYPE_SEMINAR => 'Seminar Room',
            self::TYPE_AUDITORIUM => 'Auditorium',
            self::TYPE_WORKSHOP => 'Workshop',
        ];
    }

    /**
     * Scope for active classrooms.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for available classrooms.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('status', true);
    }

    /**
     * Scope for classrooms by department.
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope for classrooms by school (through department).
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->whereHas('department', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        });
    }

    /**
     * Scope for classrooms by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for classrooms with minimum capacity.
     */
    public function scopeWithMinCapacity($query, $capacity)
    {
        return $query->where('capacity', '>=', $capacity);
    }

    /**
     * Get the full location string.
     */
    public function getFullLocationAttribute(): string
    {
        $parts = [];

        if ($this->building) {
            $parts[] = $this->building;
        }

        if ($this->floor) {
            $parts[] = "Floor {$this->floor}";
        }

        $parts[] = $this->name;

        return implode(', ', $parts);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
