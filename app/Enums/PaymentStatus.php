<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum Dibayar',
            self::Paid => 'Lunas',
            self::Failed => 'Gagal',
            self::Refunded => 'Dikembalikan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Unpaid => 'amber',
            self::Paid => 'emerald',
            self::Failed => 'red',
            self::Refunded => 'gray',
        };
    }
}
