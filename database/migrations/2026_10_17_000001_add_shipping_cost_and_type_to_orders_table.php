<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('product_subtotal', 12, 2)->nullable()->after('total_price');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('shipping_date');
            $table->string('shipping_cost_type', 50)->default('buyer_to_driver')->after('shipping_cost');
        });

        // Initialize product_subtotal from current total_price for existing orders
        DB::statement('UPDATE orders SET product_subtotal = total_price WHERE product_subtotal IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['product_subtotal', 'shipping_cost', 'shipping_cost_type']);
        });
    }
};
