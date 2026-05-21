<?php

namespace Src\Domain\OrderDomain\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case Completed = 'completed';

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
            self::Pending => 'Pending',
            self::Assigned => 'Assigned',
            self::Completed => 'Completed',
        };
    }

    /**
     * Check if order can be assigned
     */
    public function canBeAssigned(): bool
    {
        return $this === self::Pending;
    }

    /**
     * Check if order can be completed
     */
    public function canBeCompleted(): bool
    {
        return $this === self::Assigned;
    }
}
