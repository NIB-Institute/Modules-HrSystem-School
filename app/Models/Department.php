<?php

namespace Modules\School\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\Location;

class Department extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'school_departments';

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'total_students' => 0,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'school_id',
        'name',
        'code',
        'description',
        'head_of_department',
        'class_room_id',
        'email',
        'phone',
        'office_location',
        'latitude',
        'longitude',
        'geofence_radius',
        'enforce_geofence',
        'timezone',
        'established_year',
        'total_students',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'established_year' => 'integer',
        'total_students' => 'integer',
        'status' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'geofence_radius' => 'integer',
        'enforce_geofence' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['staff_count'];

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

        // Ensure nullable integers have defaults before save
        static::saving(function ($model) {
            if (is_null($model->total_students)) {
                $model->total_students = 0;
            }
        });
    }

    /**
     * Get the school that owns the department.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the programs for the department.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the courses for the department.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the employees/staff for the department.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the staff count (computed from employees relationship).
     */
    public function getStaffCountAttribute(): int
    {
        return $this->employees()->count();
    }

    /**
     * Scope for active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get the classrooms for the department.
     */
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    /**
     * Get the geofence location for the department.
     *
     * Uses the existing Location system with polymorphic relationship.
     * Location provides comprehensive geofence support (circle, polygon, dynamic).
     */
    public function location(): MorphOne
    {
        return $this->morphOne(Location::class, 'locationable');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Check if a GPS coordinate is within the department's geofence.
     *
     * Uses the linked Location model for comprehensive geofence verification.
     * Supports circle, polygon, and dynamic geofence types.
     *
     * @param float|null $lat Latitude to check
     * @param float|null $lng Longitude to check
     * @return array Geofence verification result
     */
    public function isWithinGeofence(?float $lat, ?float $lng): array
    {
        // Use linked Location for geofence verification
        $location = $this->location;

        if ($location) {
            return $location->verifyLocation($lat, $lng);
        }

        // Fallback to direct fields if no Location linked (backwards compatibility)
        if (!$this->latitude || !$this->longitude) {
            return [
                'verified' => true,
                'within_geofence' => false,
                'distance_meters' => null,
                'geofence_radius' => $this->geofence_radius ?? 100,
                'geofence_type' => 'none',
                'message' => 'No geofence configured',
            ];
        }

        // If no scan location provided
        if (!$lat || !$lng) {
            return [
                'verified' => !$this->enforce_geofence,
                'within_geofence' => false,
                'distance_meters' => null,
                'geofence_radius' => $this->geofence_radius,
                'geofence_type' => 'circle',
                'message' => $this->enforce_geofence
                    ? 'Location required for attendance'
                    : 'Location not provided (optional)',
            ];
        }

        // Calculate distance using Haversine formula
        $distance = $this->calculateDistance($this->latitude, $this->longitude, $lat, $lng);
        $withinGeofence = $distance <= $this->geofence_radius;

        return [
            'verified' => $withinGeofence || !$this->enforce_geofence,
            'within_geofence' => $withinGeofence,
            'distance_meters' => round($distance, 2),
            'geofence_radius' => $this->geofence_radius,
            'geofence_type' => 'circle',
            'message' => $withinGeofence
                ? 'Location verified'
                : sprintf('Outside geofence (%.0fm away, max %dm)', $distance, $this->geofence_radius),
        ];
    }

    /**
     * Calculate distance between two GPS coordinates using Haversine formula.
     *
     * @param float $lat1 First latitude
     * @param float $lng1 First longitude
     * @param float $lat2 Second latitude
     * @param float $lng2 Second longitude
     * @return float Distance in meters
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if department has geofence configured.
     *
     * Checks linked Location first, then falls back to direct fields.
     */
    public function hasGeofence(): bool
    {
        // Check linked Location first
        if ($this->location) {
            return true;
        }

        // Fallback to direct fields
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Get Google Maps URL for the department location.
     */
    public function getGoogleMapsUrl(): ?string
    {
        // Use linked Location first
        if ($this->location) {
            return $this->location->google_maps_url;
        }

        // Fallback to direct fields
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        return sprintf(
            'https://www.google.com/maps?q=%s,%s',
            $this->latitude,
            $this->longitude
        );
    }
}
