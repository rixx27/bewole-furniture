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
            $table->foreignId('product_id')->nullable()->change();
            $table->text('shipping_address')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->decimal('total_price', 12, 2)->default(0)->change();

            $table->boolean('is_custom')->default(false)->after('order_code');
            $table->string('custom_furniture_type', 255)->nullable()->after('is_custom');
            $table->string('custom_dimensions', 255)->nullable()->after('custom_furniture_type');
            $table->string('custom_design_image', 255)->nullable()->after('custom_dimensions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_custom',
                'custom_furniture_type',
                'custom_dimensions',
                'custom_design_image',
            ]);

            $table->foreignId('product_id')->nullable(false)->change();
            $table->text('shipping_address')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
        });
    }
};
