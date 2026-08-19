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
     * Get the next sequential status in the order workflow.
     */
    public function getNextStatus(): ?self
    {
        return match ($this) {
            self::Pending => self::Confirmed,
            self::Confirmed => self::AwaitingPayment,
            self::AwaitingPayment => self::PaymentReceived,
            self::PaymentReceived => self::InProduction,
            self::InProduction => self::QualityControl,
            self::QualityControl => self::ReadyToShip,
            self::ReadyToShip => self::Shipped,
            self::Shipped => self::Completed,
            self::Completed => null,
            self::Cancelled => null,
        };
    }

    /**
     * Check if status can transition to target status.
     * Rules:
     * - Order status can only advance to the immediate next step in sequence.
     * - Status cannot go backwards.
     * - Status cannot skip steps forward.
     * - Status can jump to Cancelled from any non-final status.
     * - Completed and Cancelled are final statuses and cannot transition to any other status.
     */
    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return false;
        }

        if ($this === self::Completed || $this === self::Cancelled) {
            return false;
        }

        if ($target === self::Cancelled) {
            return true;
        }

        return $target === $this->getNextStatus();
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
