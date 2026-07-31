<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case ReadyToShip = 'ready_to_ship';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * Get label in Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Dikonfirmasi',
            self::Processing => 'Diproses',
            self::ReadyToShip => 'Siap Dikirim',
            self::Shipped => 'Dikirim',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Get color class for status badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Confirmed => 'blue',
            self::Processing => 'indigo',
            self::ReadyToShip => 'cyan',
            self::Shipped => 'violet',
            self::Completed => 'emerald',
            self::Cancelled => 'red',
        };
    }

    /**
     * Check if status can transition to target status.
     */
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => in_array($target, [self::Confirmed, self::Cancelled]),
            self::Confirmed => in_array($target, [self::Processing, self::Cancelled]),
            self::Processing => in_array($target, [self::ReadyToShip, self::Cancelled]),
            self::ReadyToShip => in_array($target, [self::Shipped, self::Cancelled]),
            self::Shipped => in_array($target, [self::Completed]),
            self::Completed => false,
            self::Cancelled => false,
        };
    }

    /**
     * Get all statuses that are considered "active" (not final).
     */
    public static function activeStatuses(): array
    {
        return [
            self::Pending,
            self::Confirmed,
            self::Processing,
            self::ReadyToShip,
            self::Shipped,
        ];
    }
}
