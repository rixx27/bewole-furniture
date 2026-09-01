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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->longText('about')->nullable();
            $table->text('vision')->nullable();
            $table->string('company_image')->nullable();
            $table->timestamps();
        });

        Schema::create('company_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('company_advantages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('company_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained()->cascadeOnDelete();
            $table->string('icon', 100);
            $table->string('title', 255);
            $table->enum('type', ['auto', 'manual'])->default('auto');
            $table->string('source', 50)->nullable();
            $table->string('manual_value', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_statistics');
        Schema::dropIfExists('company_advantages');
        Schema::dropIfExists('company_missions');
        Schema::dropIfExists('company_profiles');
    }
};
