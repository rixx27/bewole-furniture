<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case DownPayment = 'down_payment';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum Dibayar',
            self::DownPayment => 'DP (Uang Muka)',
            self::Paid => 'Lunas',
            self::Failed => 'Gagal / Ditolak',
            self::Refunded => 'Dikembalikan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Unpaid => 'amber',
            self::DownPayment => 'indigo',
            self::Paid => 'emerald',
            self::Failed => 'red',
            self::Refunded => 'gray',
        };
    }
}
