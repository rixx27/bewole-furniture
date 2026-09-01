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
        // Add material usage per product to products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'seat_material_usage')) {
                $table->decimal('seat_material_usage', 8, 2)->default(0.8)->after('material');
            }
            if (!Schema::hasColumn('products', 'packing_material_usage')) {
                $table->decimal('packing_material_usage', 8, 2)->default(1.2)->after('seat_material_usage');
            }
        });

        // Create product_materials table
        if (!Schema::hasTable('product_materials')) {
            Schema::create('product_materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
                $table->string('type', 50); // seat_material, packing_material
                $table->string('name', 255);
                $table->decimal('price_per_meter', 12, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Add snapshot fields to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'seat_material_name')) {
                $table->string('seat_material_name', 255)->nullable()->after('packing_price');
            }
            if (!Schema::hasColumn('order_items', 'seat_price_per_meter')) {
                $table->decimal('seat_price_per_meter', 12, 2)->default(0)->after('seat_material_name');
            }
            if (!Schema::hasColumn('order_items', 'seat_usage_meter')) {
                $table->decimal('seat_usage_meter', 8, 2)->default(0)->after('seat_price_per_meter');
            }
            if (!Schema::hasColumn('order_items', 'seat_material_cost')) {
                $table->decimal('seat_material_cost', 12, 2)->default(0)->after('seat_usage_meter');
            }
            if (!Schema::hasColumn('order_items', 'packing_material_name')) {
                $table->string('packing_material_name', 255)->nullable()->after('seat_material_cost');
            }
            if (!Schema::hasColumn('order_items', 'packing_price_per_meter')) {
                $table->decimal('packing_price_per_meter', 12, 2)->default(0)->after('packing_material_name');
            }
            if (!Schema::hasColumn('order_items', 'packing_usage_meter')) {
                $table->decimal('packing_usage_meter', 8, 2)->default(0)->after('packing_price_per_meter');
            }
            if (!Schema::hasColumn('order_items', 'packing_material_cost')) {
                $table->decimal('packing_material_cost', 12, 2)->default(0)->after('packing_usage_meter');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $columns = [
                'seat_material_name',
                'seat_price_per_meter',
                'seat_usage_meter',
                'seat_material_cost',
                'packing_material_name',
                'packing_price_per_meter',
                'packing_usage_meter',
                'packing_material_cost',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('order_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('product_materials');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'seat_material_usage')) {
                $table->dropColumn('seat_material_usage');
            }
            if (Schema::hasColumn('products', 'packing_material_usage')) {
                $table->dropColumn('packing_material_usage');
            }
        });
    }
};
