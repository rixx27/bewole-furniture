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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price_matang')) {
                $table->bigInteger('price_matang')->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'packing_fee')) {
                $table->bigInteger('packing_fee')->default(0)->after('price_matang');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'packing_fee')) {
                $table->dropColumn('packing_fee');
            }
            if (Schema::hasColumn('products', 'price_matang')) {
                $table->dropColumn('price_matang');
            }
        });
    }
};
