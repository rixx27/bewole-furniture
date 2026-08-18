<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Updates existing data to match new enum values and order code format.
     */
    public function up(): void
    {
        // 1. Update status values in orders table
        DB::table('orders')
            ->where('status', 'awaiting_payment')
            ->update(['status' => 'waiting_payment']);

        DB::table('orders')
            ->where('status', 'in_production')
            ->update(['status' => 'processing']);

        // 2. Update status values in order_status_histories table
        DB::table('order_status_histories')
            ->where('status', 'awaiting_payment')
            ->update(['status' => 'waiting_payment']);

        DB::table('order_status_histories')
            ->where('status', 'in_production')
            ->update(['status' => 'processing']);

        // 3. Update order codes from ORD-YYYYMMDD-XXXX to BWL-MMDDYY-XXXX
        $orders = DB::table('orders')
            ->where('order_code', 'like', 'ORD-%')
            ->get(['id', 'order_code']);

        foreach ($orders as $order) {
            // Parse: ORD-YYYYMMDD-XXXX
            if (preg_match('/^ORD-(\d{4})(\d{2})(\d{2})-(\d{4})$/', $order->order_code, $matches)) {
                $year = substr($matches[1], 2); // YY from YYYY
                $month = $matches[2];            // MM
                $day = $matches[3];              // DD
                $seq = $matches[4];              // XXXX

                // New format: BWL-MMDDYY-XXXX
                $newCode = "BWL-{$month}{$day}{$year}-{$seq}";

                DB::table('orders')
                    ->where('id', $order->id)
                    ->update(['order_code' => $newCode]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status values
        DB::table('orders')
            ->where('status', 'waiting_payment')
            ->update(['status' => 'awaiting_payment']);

        DB::table('orders')
            ->where('status', 'processing')
            ->update(['status' => 'in_production']);

        DB::table('order_status_histories')
            ->where('status', 'waiting_payment')
            ->update(['status' => 'awaiting_payment']);

        DB::table('order_status_histories')
            ->where('status', 'processing')
            ->update(['status' => 'in_production']);

        // Revert order codes from BWL-MMDDYY-XXXX to ORD-YYYYMMDD-XXXX
        $orders = DB::table('orders')
            ->where('order_code', 'like', 'BWL-%')
            ->get(['id', 'order_code']);

        foreach ($orders as $order) {
            if (preg_match('/^BWL-(\d{2})(\d{2})(\d{2})-(\d{4})$/', $order->order_code, $matches)) {
                $month = $matches[1];
                $day = $matches[2];
                $year = '20' . $matches[3];
                $seq = $matches[4];

                $oldCode = "ORD-{$year}{$month}{$day}-{$seq}";

                DB::table('orders')
                    ->where('id', $order->id)
                    ->update(['order_code' => $oldCode]);
            }
        }
    }
};
