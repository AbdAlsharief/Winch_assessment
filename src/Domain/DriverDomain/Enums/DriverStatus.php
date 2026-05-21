<?php

namespace Src\Domain\DriverDomain\Enums;

enum DriverStatus: string
{
    case Available = 'available';
    case Busy = 'busy';

    /**
     * Get all possible values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::Available => 'Available',
            self::Busy => 'Busy',
        };
    }
}
