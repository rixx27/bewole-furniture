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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('order_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('shipping_address');
            $table->string('city');
            $table->string('postal_code')->nullable();

            // Customization & Packing
            $table->string('meubel_type', 50)->nullable();
            $table->string('packing_type', 50)->nullable();
            $table->json('customization_details')->nullable();
            $table->decimal('customization_fee', 12, 2)->default(0);
            $table->decimal('packing_fee', 12, 2)->default(0);

            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total_price', 12, 2);
            $table->text('notes')->nullable();

            $table->string('status', 50)->default('pending');
            $table->string('payment_status', 50)->default('unpaid');
            $table->string('payment_method', 50)->nullable();
            $table->string('shipping_method', 50)->nullable();

            // Expedition fields
            $table->string('courier')->nullable();
            $table->string('tracking_number')->nullable();

            // Internal delivery fields
            $table->string('driver_name')->nullable();
            $table->string('vehicle_number')->nullable();

            // Date fields
            $table->date('shipping_date')->nullable();
            $table->date('pickup_date')->nullable();

            $table->string('whatsapp_number')->nullable();

            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->string('meubel_type', 50)->nullable();
            $table->string('customization_option', 255)->nullable();
            $table->string('packing_type', 50)->nullable();
            $table->decimal('customization_price', 12, 2)->default(0);
            $table->decimal('packing_price', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);

            // Material snapshot fields
            $table->string('seat_material_name', 255)->nullable();
            $table->decimal('seat_price_per_meter', 12, 2)->default(0);
            $table->decimal('seat_usage_meter', 8, 2)->default(0);
            $table->decimal('seat_material_cost', 12, 2)->default(0);
            $table->string('packing_material_name', 255)->nullable();
            $table->decimal('packing_price_per_meter', 12, 2)->default(0);
            $table->decimal('packing_usage_meter', 8, 2)->default(0);
            $table->decimal('packing_material_cost', 12, 2)->default(0);

            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->text('description')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
