<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderCodeGenerator
{
    /**
     * Generate a unique order code with format: ORD-YYYYMMDD-XXXX
     * Example: ORD-20260801-0001
     */
    public function generate(): string
    {
        $date = now()->format('Ymd');
        $prefix = "ORD-{$date}-";

        // Get the last order code for today using database locking to prevent duplicates
        $lastOrder = Order::where('order_code', 'like', "{$prefix}%")
            ->orderBy('order_code', 'desc')
            ->lockForUpdate()
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $orderCode = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Ensure uniqueness (double-check)
        $attempts = 0;
        while (Order::where('order_code', $orderCode)->exists() && $attempts < 10) {
            $newNumber++;
            $orderCode = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $attempts++;
        }

        return $orderCode;
    }
}
