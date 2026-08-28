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
            $table->string('meubel_type', 50)->nullable()->after('postal_code');
            $table->string('packing_type', 50)->nullable()->after('meubel_type');
            $table->json('customization_details')->nullable()->after('packing_type');
            $table->decimal('customization_fee', 12, 2)->default(0)->after('customization_details');
            $table->decimal('packing_fee', 12, 2)->default(0)->after('customization_fee');
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
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'meubel_type',
                'packing_type',
                'customization_details',
                'customization_fee',
                'packing_fee',
            ]);
        });
    }
};
