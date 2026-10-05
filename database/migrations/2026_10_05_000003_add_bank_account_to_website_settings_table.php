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
        Schema::table('website_settings', function (Blueprint $table) {
            $table->string('bank_name', 100)->nullable()->after('whatsapp');
            $table->string('bank_account_number', 100)->nullable()->after('bank_name');
            $table->string('bank_account_holder', 150)->nullable()->after('bank_account_number');
        });

        // Set default values for existing records
        try {
            DB::table('website_settings')->update([
                'bank_name' => 'BCA (Bank Central Asia)',
                'bank_account_number' => '8910-2345-6789',
                'bank_account_holder' => 'CV BEWOLE JEPARA FURNITURE',
            ]);
        } catch (\Throwable $e) {
            // Ignore if no records exist yet
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'bank_account_number',
                'bank_account_holder',
            ]);
        });
    }
};
