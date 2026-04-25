<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'school_equipment';

    /**
     * Equipment categories.
     */
    public const CATEGORY_TECHNOLOGY = 'technology';
    public const CATEGORY_FURNITURE = 'furniture';
    public const CATEGORY_SAFETY = 'safety';
    public const CATEGORY_ACCESSIBILITY = 'accessibility';
    public const CATEGORY_OTHER = 'other';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'icon',
        'qty',
        'price_total',
        'description',
        'category',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'category' => self::CATEGORY_OTHER,
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
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && !$model->isDirty('slug')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Get the classrooms that have this equipment.
     */
    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'school_classroom_equipment')
            ->withPivot(['value', 'quantity', 'notes'])
            ->withTimestamps();
    }

    /**
     * Get available categories.
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_TECHNOLOGY => 'Technology',
            self::CATEGORY_FURNITURE => 'Furniture',
            self::CATEGORY_SAFETY => 'Safety',
            self::CATEGORY_ACCESSIBILITY => 'Accessibility',
            self::CATEGORY_OTHER => 'Other',
        ];
    }

    /**
     * Scope for active equipment.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for equipment by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
