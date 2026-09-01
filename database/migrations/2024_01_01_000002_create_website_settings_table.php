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
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();

            // Section 1: Identitas Website
            $table->string('logo')->nullable();
            $table->string('site_name')->nullable();
            $table->string('site_tagline')->nullable();

            // Section 2: Branding (Login Page)
            $table->string('login_background')->nullable();
            $table->text('login_quote')->nullable();

            // Section 3: Informasi Kontak
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('address')->nullable();
            $table->text('google_maps_embed')->nullable();

            // Section 4: Media Sosial
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();

            // Section 5: Jam Operasional
            $table->string('working_days')->nullable();
            $table->string('working_hours')->nullable();

            // Section 6: Maintenance Mode
            $table->boolean('is_maintenance')->default(false);
            $table->text('maintenance_message')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
