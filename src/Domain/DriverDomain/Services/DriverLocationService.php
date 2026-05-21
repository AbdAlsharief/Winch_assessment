<?php

namespace Src\Domain\DriverDomain\Services;

use Illuminate\Support\Facades\DB;
use Src\Domain\DriverDomain\Contracts\DriverLocationContract;
use Src\Domain\DriverDomain\Enums\DriverStatus;
use Src\Domain\DriverDomain\Models\Driver;
use Src\Domain\OrderDomain\Enums\OrderStatus;

class DriverLocationService implements DriverLocationContract
{
    /**
     * Find the nearest available driver to the given coordinates.
     *
     * Uses MySQL's ST_Distance_Sphere for optimized geospatial calculations.
     * Filters drivers who are:
     * - Status is 'available'
     * - Have no active (pending or assigned) orders
     *
     * @param float $latitude The latitude of the pickup location
     * @param float $longitude The longitude of the pickup location
     * @param float|null $maxDistanceKm Maximum search radius in kilometers (optional)
     * @return Driver|null The nearest available driver or null if none found
     */
    public function findNearestAvailableDriver(
        float $latitude,
        float $longitude,
        ?float $maxDistanceKm = null
    ): ?Driver {
        $point = "POINT($longitude $latitude)";
        $maxDistanceMeters = $maxDistanceKm ? $maxDistanceKm * 1000 : null;

        $query = Driver::query()
            ->select([
                'drivers.*',
                DB::raw("ST_Distance_Sphere(
                    location,
                    ST_GeomFromText(?, 4326)
                ) as distance") // Distance in meters
            ])
            ->setBindings([$point])
            ->where('status', DriverStatus::Available)
            // Exclude drivers with active orders (pending or assigned)
            ->whereDoesntHave('orders', function ($query) {
                $query->whereIn('status', [
                    OrderStatus::Pending,
                    OrderStatus::Assigned,
                ]);
            });

        // Apply maximum distance filter if specified
        if ($maxDistanceMeters !== null) {
            $query->havingRaw('distance <= ?', [$maxDistanceMeters]);
        }

        return $query
            ->orderBy('distance', 'asc')
            ->first();
    }

    /**
     * Find all available drivers within a radius.
     *
     * @param float $latitude The latitude of the pickup location
     * @param float $longitude The longitude of the pickup location
     * @param float $radiusKm Search radius in kilometers
     * @param int $limit Maximum number of drivers to return
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAvailableDriversWithinRadius(
        float $latitude,
        float $longitude,
        float $radiusKm,
        int $limit = 10
    ): \Illuminate\Database\Eloquent\Collection {
        $point = "POINT($longitude $latitude)";
        $radiusMeters = $radiusKm * 1000;

        return Driver::query()
            ->select([
                'drivers.*',
                DB::raw("ST_Distance_Sphere(
                    location,
                    ST_GeomFromText(?, 4326)
                ) as distance")
            ])
            ->setBindings([$point])
            ->where('status', DriverStatus::Available)
            ->whereDoesntHave('orders', function ($query) {
                $query->whereIn('status', [
                    OrderStatus::Pending,
                    OrderStatus::Assigned,
                ]);
            })
            ->havingRaw('distance <= ?', [$radiusMeters])
            ->orderBy('distance', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate distance between two points using Haversine formula.
     * Fallback method if spatial functions are not available.
     *
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @return float Distance in kilometers
     */
    public function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
