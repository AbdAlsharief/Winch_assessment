<?php

namespace Src\Domain\OrderDomain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Domain\OrderDomain\Enums\OrderStatus;
use Src\Domain\DriverDomain\Models\Driver;

class Order extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'status',
        'pickup_latitude',
        'pickup_longitude',
        'driver_id',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'pickup_latitude' => 'decimal:8',
            'pickup_longitude' => 'decimal:8',
        ];
    }

    /**
     * Get the driver assigned to this order.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    /**
     * Check if order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    /**
     * Check if order is assigned.
     */
    public function isAssigned(): bool
    {
        return $this->status === OrderStatus::Assigned;
    }

    /**
     * Check if order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::Completed;
    }

    /**
     * Assign order to a driver.
     */
    public function assignToDriver(Driver $driver): bool
    {
        if (!$this->status->canBeAssigned()) {
            return false;
        }

        return $this->update([
            'driver_id' => $driver->id,
            'status' => OrderStatus::Assigned,
        ]);
    }

    /**
     * Mark order as completed.
     */
    public function markAsCompleted(): bool
    {
        if (!$this->status->canBeCompleted()) {
            return false;
        }

        return $this->update(['status' => OrderStatus::Completed]);
    }

    /**
     * Unassign driver from order.
     */
    public function unassignDriver(): bool
    {
        if ($this->isPending() || $this->isCompleted()) {
            return false;
        }

        return $this->update([
            'driver_id' => null,
            'status' => OrderStatus::Pending,
        ]);
    }
}
