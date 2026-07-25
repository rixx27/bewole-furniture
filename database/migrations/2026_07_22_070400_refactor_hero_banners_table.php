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
        Schema::table('hero_banners', function (Blueprint $table) {
            // Rename existing fields
            $table->renameColumn('button_text', 'primary_button_text');
            $table->renameColumn('button_url', 'primary_button_link');

            // Add new fields
            $table->string('secondary_button_text')->nullable()->after('primary_button_link');
            $table->string('secondary_button_link')->nullable()->after('secondary_button_text');
            $table->string('badge_text')->nullable()->after('secondary_button_link');
            $table->enum('text_position', ['left', 'center', 'right'])->default('center')->after('badge_text');
            $table->integer('overlay_opacity')->default(40)->after('text_position');
            $table->string('status')->default('inactive')->after('overlay_opacity');

            // Drop old fields
            $table->dropColumn(['description', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_banners', function (Blueprint $table) {
            $table->renameColumn('primary_button_text', 'button_text');
            $table->renameColumn('primary_button_link', 'button_url');

            $table->text('description')->nullable()->after('subtitle');
            $table->boolean('is_active')->default(true)->after('sort_order');

            $table->dropColumn([
                'secondary_button_text',
                'secondary_button_link',
                'badge_text',
                'text_position',
                'overlay_opacity',
                'status',
            ]);
        });
    }
};

