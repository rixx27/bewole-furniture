<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Re-add discount_percentage column
        if (!Schema::hasColumn('products', 'discount_percentage')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('discount_percentage')->nullable()->after('price');
            });
        }

        // 2. Revert price columns from decimal(15,2) back to decimal(12,2)
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->change();
            $table->decimal('discount_price', 12, 2)->nullable()->change();
        });

        // 3. Clean up statuses
        // Convert 'inactive' to 'sold_out' since we only support 3 statuses
        DB::statement("UPDATE products SET status = 'sold_out' WHERE status = 'inactive'");

        // 4. Auto-fix: If stock = 0 and status = 'active', change to 'pre_order'
        DB::statement("UPDATE products SET status = 'pre_order' WHERE stock = 0 AND status = 'active'");

        // 5. Auto-fix: If stock > 0 and status = 'pre_order', change to 'active'
        DB::statement("UPDATE products SET status = 'active' WHERE stock > 0 AND status = 'pre_order'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            $table->decimal('price', 15, 2)->change();
            $table->decimal('discount_price', 15, 2)->nullable()->change();
        });
    }
};

