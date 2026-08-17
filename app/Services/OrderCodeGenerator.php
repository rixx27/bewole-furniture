<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderCodeGenerator
{
    /**
    * Generate a unique order code with format: BWL-DD-MM-YY-D
    * Example: BWL-14-08-26-1, BWL-14-08-26-2, BWL-15-09-26-1
    *
    * - BWL  : fixed prefix for Bewole
    * - DD   : 2-digit day of order creation
    * - MM   : 2-digit month of order creation
    * - YY   : 2-digit year of order creation
    * - D    : auto-increment sequence per day (no zero-padding)
     */
    public function generate(): string
    {
        return DB::transaction(function () {
            $now    = now();
            $dd     = $now->format('d');   // 2-digit day,   e.g. "14"
            $mm     = $now->format('m');   // 2-digit month, e.g. "08"
            $yy     = $now->format('y');   // 2-digit year,  e.g. "26"

            // Include day so sequence resets every calendar day
            $prefix = "BWL-{$dd}-{$mm}-{$yy}-";

            // Lock the latest order for this month/year to prevent race conditions
            $lastOrder = Order::where('order_code', 'like', "{$prefix}%")
                ->orderByRaw('CAST(SUBSTRING_INDEX(order_code, \'-\', -1) AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();

            $newNumber = $lastOrder
                ? ((int) substr($lastOrder->order_code, strrpos($lastOrder->order_code, '-') + 1)) + 1
                : 1;

            $orderCode = $prefix . $newNumber;

            // Double-check uniqueness and increment if somehow still taken
            $attempts = 0;
            while (Order::where('order_code', $orderCode)->exists() && $attempts < 20) {
                $newNumber++;
                $orderCode = $prefix . $newNumber;
                $attempts++;
            }

            return $orderCode;
        });
    }
}
