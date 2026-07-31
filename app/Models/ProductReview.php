<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReview extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'comment',
        'is_verified',
        'is_visible',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_verified' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    /**
     * Get the user that wrote the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that this review belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order associated with this review.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the images for the review.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductReviewImage::class);
    }

    // ============ SCOPES ============

    /**
     * Scope a query to only include visible reviews.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope a query to only include hidden reviews.
     */
    public function scopeHidden($query)
    {
        return $query->where('is_visible', false);
    }

    /**
     * Scope a query to only include verified reviews.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include reviews with a specific rating.
     */
    public function scopeWhereRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ============ ACCESSORS ============

    /**
     * Get the rating stars as HTML.
     */
    public function getRatingStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<svg class="h-4 w-4 text-yellow-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            } else {
                $stars .= '<svg class="h-4 w-4 text-gray-300 dark:text-gray-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            }
        }
        return $stars;
    }

    /**
     * Get the rating label in Bahasa Indonesia.
     */
    public function getRatingLabelAttribute(): string
    {
        return match ($this->rating) {
            1 => 'Sangat Buruk',
            2 => 'Buruk',
            3 => 'Cukup',
            4 => 'Baik',
            5 => 'Sangat Baik',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get the visibility status label.
     */
    public function getVisibilityLabelAttribute(): string
    {
        return $this->is_visible ? 'Ditampilkan' : 'Disembunyikan';
    }

    /**
     * Get the visibility status color.
     */
    public function getVisibilityColorAttribute(): string
    {
        return $this->is_visible ? 'emerald' : 'gray';
    }

    /**
     * Get a truncated comment for table display.
     */
    public function getExcerptAttribute(): string
    {
        $length = 80;

        if (! $this->comment) {
            return '-';
        }

        return strlen($this->comment) > $length
            ? substr($this->comment, 0, $length) . '...'
            : $this->comment;
    }

    // ============ STATIC METHODS ============

    /**
     * Get the average rating attribute.
     */
    public static function getAverageRatingForProduct(int $productId): float
    {
        return static::where('product_id', $productId)
            ->where('is_visible', true)
            ->avg('rating') ?? 0.0;
    }

    /**
     * Get the total reviews count for a product.
     */
    public static function getReviewsCountForProduct(int $productId): int
    {
        return static::where('product_id', $productId)
            ->where('is_visible', true)
            ->count();
    }
}

