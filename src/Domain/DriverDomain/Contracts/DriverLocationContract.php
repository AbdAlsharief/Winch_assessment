<?php

namespace Src\Domain\DriverDomain\Contracts;

use Src\Domain\DriverDomain\Models\Driver;

interface DriverLocationContract
{
    /**
     * Find the nearest available driver to the given coordinates.
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
    ): ?Driver;
}
