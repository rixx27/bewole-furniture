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
            $table->integer('discount_percentage')->nullable()->after('price');
            $table->string('status')->default('active')->after('discount_price');
        });

        // Migrate existing is_active data to status
        DB::statement("UPDATE products SET status = 'active' WHERE is_active = 1");
        DB::statement("UPDATE products SET status = 'inactive' WHERE is_active = 0");

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('thumbnail');
        });

        DB::statement("UPDATE products SET is_active = 1 WHERE status = 'active'");
        DB::statement("UPDATE products SET is_active = 0 WHERE status != 'active'");

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_percentage', 'status']);
        });
    }
};

