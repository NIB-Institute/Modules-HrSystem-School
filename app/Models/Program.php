<?php

namespace Modules\School\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Program extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'school_programs';

    /**
     * Program degree levels.
     */
    public const LEVEL_CERTIFICATE = 'certificate';
    public const LEVEL_DIPLOMA = 'diploma';
    public const LEVEL_ASSOCIATE = 'associate';
    public const LEVEL_BACHELOR = 'bachelor';
    public const LEVEL_MASTER = 'master';
    public const LEVEL_DOCTORATE = 'doctorate';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'school_id',
        'department_id',
        'name',
        'code',
        'description',
        'degree_level',
        'duration_years',
        'credits_required',
        'tuition_fee',
        'admission_requirements',
        'program_coordinator',
        'max_students',
        'current_enrollment',
        'accreditation_status',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'duration_years' => 'integer',
        'credits_required' => 'integer',
        'tuition_fee' => 'decimal:2',
        'max_students' => 'integer',
        'current_enrollment' => 'integer',
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
     * Get the school that owns the program.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the department that owns the program.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the courses for the program.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get available degree levels.
     */
    public static function getDegreeLevels(): array
    {
        return [
            self::LEVEL_CERTIFICATE => 'Certificate',
            self::LEVEL_DIPLOMA => 'Diploma',
            self::LEVEL_ASSOCIATE => 'Associate Degree',
            self::LEVEL_BACHELOR => 'Bachelor\'s Degree',
            self::LEVEL_MASTER => 'Master\'s Degree',
            self::LEVEL_DOCTORATE => 'Doctorate',
        ];
    }

    /**
     * Scope for active programs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
