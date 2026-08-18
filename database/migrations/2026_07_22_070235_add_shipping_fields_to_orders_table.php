<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add shipping method enum
            $table->enum('shipping_method', [
                'expedition',
                'internal_delivery',
                'self_pickup',
            ])->nullable()->after('payment_method');

            // Expedition fields
            $table->string('courier')->nullable()->after('shipping_method');
            $table->string('tracking_number')->nullable()->after('courier');

            // Internal delivery fields
            $table->string('driver_name')->nullable()->after('tracking_number');
            $table->string('vehicle_number')->nullable()->after('driver_name');

            // Date fields
            $table->date('shipping_date')->nullable()->after('vehicle_number');
            $table->date('pickup_date')->nullable()->after('shipping_date');

            // Update status enum to include new statuses
            // Note: MySQL doesn't allow modifying enums directly, so we use raw SQL
        });

        // Update the status enum to include 'ready_to_ship' and change 'shipping' to 'shipped'
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'ready_to_ship', 'shipped', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_method',
                'courier',
                'tracking_number',
                'driver_name',
                'vehicle_number',
                'shipping_date',
                'pickup_date',
            ]);
        });

        // Revert status enum back to original
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'shipping', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};

