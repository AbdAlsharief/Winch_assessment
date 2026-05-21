<?php

namespace Src\Domain\DriverDomain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Domain\DriverDomain\Enums\DriverStatus;
use Src\Domain\OrderDomain\Models\Order;

class Driver extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'drivers';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'status',
        'current_latitude',
        'current_longitude',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status' => DriverStatus::class,
            'current_latitude' => 'decimal:8',
            'current_longitude' => 'decimal:8',
        ];
    }

    /**
     * Get all orders assigned to this driver.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    /**
     * Check if driver is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === DriverStatus::Available;
    }

    /**
     * Check if driver is busy.
     */
    public function isBusy(): bool
    {
        return $this->status === DriverStatus::Busy;
    }

    /**
     * Mark driver as available.
     */
    public function markAsAvailable(): void
    {
        $this->update(['status' => DriverStatus::Available]);
    }

    /**
     * Mark driver as busy.
     */
    public function markAsBusy(): void
    {
        $this->update(['status' => DriverStatus::Busy]);
    }
}
