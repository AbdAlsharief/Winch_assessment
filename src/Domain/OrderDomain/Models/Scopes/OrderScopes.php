<?php

namespace Src\Domain\OrderDomain\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Src\Domain\OrderDomain\Enums\OrderStatus;

trait OrderScopes
{
    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Pending);
    }

    /**
     * Scope a query to only include assigned orders.
     */
    public function scopeAssigned(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Assigned);
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Completed);
    }

    /**
     * Scope a query to only include active orders (pending or assigned).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::Pending,
            OrderStatus::Assigned,
        ]);
    }

    /**
     * Scope a query to only include unassigned orders (pending without driver).
     */
    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Pending)
            ->whereNull('driver_id');
    }

    /**
     * Scope a query to filter orders by driver.
     */
    public function scopeForDriver(Builder $query, int $driverId): Builder
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * Scope a query to filter orders within a date range.
     */
    public function scopeCreatedBetween(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter orders near a location.
     */
    public function scopeNearLocation(Builder $query, float $latitude, float $longitude, float $radiusKm): Builder
    {
        $radiusMeters = $radiusKm * 1000;
        $point = "POINT($longitude $latitude)";

        return $query->selectRaw('orders.*, ST_Distance_Sphere(
                pickup_location,
                ST_GeomFromText(?, 4326)
            ) as distance', [$point])
            ->havingRaw('distance <= ?', [$radiusMeters])
            ->orderBy('distance', 'asc');
    }
}
