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
            if (!Schema::hasColumn('products', 'price_usd')) {
                $table->decimal('price_usd', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'price_matang_usd')) {
                $table->decimal('price_matang_usd', 10, 2)->nullable()->after('price_matang');
            }
            if (!Schema::hasColumn('products', 'discount_price_usd')) {
                $table->decimal('discount_price_usd', 10, 2)->nullable()->after('discount_price');
            }
        });

        Schema::table('website_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('website_settings', 'usd_rate')) {
                $table->decimal('usd_rate', 12, 2)->default(18271.95)->after('whatsapp_notification_enabled');
            }
            if (!Schema::hasColumn('website_settings', 'default_currency')) {
                $table->string('default_currency', 10)->default('IDR')->after('usd_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'discount_price_usd')) {
                $table->dropColumn('discount_price_usd');
            }
            if (Schema::hasColumn('products', 'price_matang_usd')) {
                $table->dropColumn('price_matang_usd');
            }
            if (Schema::hasColumn('products', 'price_usd')) {
                $table->dropColumn('price_usd');
            }
        });

        Schema::table('website_settings', function (Blueprint $table) {
            if (Schema::hasColumn('website_settings', 'default_currency')) {
                $table->dropColumn('default_currency');
            }
            if (Schema::hasColumn('website_settings', 'usd_rate')) {
                $table->dropColumn('usd_rate');
            }
        });
    }
};
