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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->bigInteger('price');
            $table->bigInteger('discount_price')->nullable();
            $table->integer('discount_percentage')->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('material', 255)->nullable();
            $table->string('dimensions', 255)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->string('status', 20)->default('active');
            $table->boolean('is_featured')->default(false);
            $table->integer('stock')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
