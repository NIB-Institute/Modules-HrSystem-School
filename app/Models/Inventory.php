<?php

namespace Modules\School\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * A physical inventory item — a specific instance of an Equipment type.
 *
 * Equipment models the catalogue ("we own 25 projectors"); Inventory
 * models each individual unit ("projector PRJ-002 in classroom 7").
 */
class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'school_inventories';

    /** Lifecycle status. */
    public const STATUS_IN_STOCK = 'in_stock';
    public const STATUS_IN_USE = 'in_use';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_RETIRED = 'retired';
    public const STATUS_LOST = 'lost';
    public const STATUS_DISPOSED = 'disposed';

    /** Physical condition. */
    public const CONDITION_NEW = 'new';
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_POOR = 'poor';

    protected $fillable = [
        'uuid',
        'asset_tag',
        'serial_number',
        'name',
        'equipment_id',
        'classroom_id',
        'department_id',
        'assigned_to_user_id',
        'status',
        'condition',
        'purchased_at',
        'cost',
        'vendor',
        'warranty_until',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'purchased_at' => 'date',
        'warranty_until' => 'date',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Inventory $inventory) {
            if (empty($inventory->uuid)) {
                $inventory->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_IN_STOCK => 'In Stock',
            self::STATUS_IN_USE => 'In Use',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_RETIRED => 'Retired',
            self::STATUS_LOST => 'Lost',
            self::STATUS_DISPOSED => 'Disposed',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function conditions(): array
    {
        return [
            self::CONDITION_NEW => 'New',
            self::CONDITION_GOOD => 'Good',
            self::CONDITION_FAIR => 'Fair',
            self::CONDITION_POOR => 'Poor',
        ];
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function isExpiredWarranty(): bool
    {
        return $this->warranty_until !== null
            && $this->warranty_until->isPast();
    }
}
