<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->boolean('whatsapp_notification_enabled')->default(true)->after('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn('whatsapp_notification_enabled');
        });
    }
};
