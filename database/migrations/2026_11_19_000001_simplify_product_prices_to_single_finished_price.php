<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'price_matang')) {
            DB::statement("UPDATE products SET price = price_matang WHERE price_matang IS NOT NULL AND price_matang > 0");
        }
        if (Schema::hasColumn('products', 'price_matang_usd')) {
            DB::statement("UPDATE products SET price_usd = price_matang_usd WHERE price_matang_usd IS NOT NULL AND price_matang_usd > 0");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive migration
    }
};
