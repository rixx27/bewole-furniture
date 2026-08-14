<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case AwaitingPayment = 'awaiting_payment';
    case PaymentReceived = 'payment_received';
    case InProduction = 'in_production';
    case QualityControl = 'quality_control';
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
            self::Pending => 'Menunggu Konfirmasi',
            self::Confirmed => 'Pesanan Dikonfirmasi',
            self::AwaitingPayment => 'Menunggu Pembayaran',
            self::PaymentReceived => 'Pembayaran Diterima',
            self::InProduction => 'Sedang Diproduksi',
            self::QualityControl => 'Quality Control',
            self::ReadyToShip => 'Siap Dikirim',
            self::Shipped => 'Dalam Pengiriman',
            self::Completed => 'Pesanan Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Get emoji indicator.
     */
    public function emoji(): string
    {
        return match ($this) {
            self::Pending => '🟠',
            self::Confirmed => '🔵',
            self::AwaitingPayment => '🟡',
            self::PaymentReceived => '🟣',
            self::InProduction => '🟤',
            self::QualityControl => '🟠',
            self::ReadyToShip => '🔵',
            self::Shipped => '🚚',
            self::Completed => '🟢',
            self::Cancelled => '🔴',
        };
    }

    /**
     * Get description for status.
     */
    public function description(): string
    {
        return match ($this) {
            self::Pending => 'Pesanan baru masuk. Admin belum mengonfirmasi pesanan.',
            self::Confirmed => 'Admin sudah mengecek pesanan. Pesanan diterima dan siap diproses.',
            self::AwaitingPayment => 'Customer perlu melakukan pembayaran.',
            self::PaymentReceived => 'Pembayaran sudah diterima. Pesanan masuk antrean produksi.',
            self::InProduction => 'Furniture sedang dibuat oleh pengrajin. Cocok untuk furniture custom.',
            self::QualityControl => 'Produk sedang diperiksa kualitas, ukuran, finishing, dan kelengkapannya.',
            self::ReadyToShip => 'Furniture sudah selesai dan siap dikirim/diambil.',
            self::Shipped => 'Barang sudah dikirim ke alamat customer.',
            self::Completed => 'Barang sudah diterima customer.',
            self::Cancelled => 'Pesanan dibatalkan oleh customer/admin.',
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
            self::AwaitingPayment => 'yellow',
            self::PaymentReceived => 'purple',
            self::InProduction => 'stone',
            self::QualityControl => 'orange',
            self::ReadyToShip => 'cyan',
            self::Shipped => 'indigo',
            self::Completed => 'emerald',
            self::Cancelled => 'red',
        };
    }

    /**
     * Check if status can transition to target status.
     */
    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return false;
        }

        return match ($this) {
            self::Pending => in_array($target, [self::Confirmed, self::AwaitingPayment, self::Cancelled]),
            self::Confirmed => in_array($target, [self::AwaitingPayment, self::PaymentReceived, self::InProduction, self::Cancelled]),
            self::AwaitingPayment => in_array($target, [self::PaymentReceived, self::Cancelled]),
            self::PaymentReceived => in_array($target, [self::InProduction, self::Cancelled]),
            self::InProduction => in_array($target, [self::QualityControl, self::Cancelled]),
            self::QualityControl => in_array($target, [self::ReadyToShip, self::InProduction, self::Cancelled]),
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
            self::AwaitingPayment,
            self::PaymentReceived,
            self::InProduction,
            self::QualityControl,
            self::ReadyToShip,
            self::Shipped,
        ];
    }
}
