<?php

use App\Models\ProductReview;
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
        Schema::table('product_reviews', function (Blueprint $table) {
            // Add new columns
            $table->text('comment')->nullable()->after('rating');
            $table->boolean('is_visible')->default(true)->after('is_active');
        });

        // Migrate data: review → comment, is_active → is_visible
        ProductReview::query()->chunk(100, function ($reviews) {
            foreach ($reviews as $review) {
                $review->update([
                    'comment' => $review->review,
                    'is_visible' => $review->is_active,
                ]);
            }
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['review', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->text('review')->nullable()->after('rating');
            $table->boolean('is_active')->default(true)->after('review');
        });

        // Migrate data back
        ProductReview::query()->chunk(100, function ($reviews) {
            foreach ($reviews as $review) {
                $review->update([
                    'review' => $review->comment,
                    'is_active' => $review->is_visible,
                ]);
            }
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropColumn(['comment', 'is_visible']);
        });
    }
};

