<?php

namespace Database\Factories;

use App\Models\ProductReview;
use App\Models\ProductReviewImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductReviewImage>
 */
class ProductReviewImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageNumber = $this->faker->numberBetween(1, 12);

        return [
            'product_review_id' => ProductReview::factory(),
            'image' => "reviews/review-sample-{$imageNumber}.jpg",
        ];
    }

    /**
     * Indicate that the image belongs to a specific review.
     */
    public function forReview(ProductReview $review): static
    {
        return $this->state(fn (array $attributes) => [
            'product_review_id' => $review->id,
        ]);
    }
}

