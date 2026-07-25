<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReviewImage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_review_id',
        'image',
    ];

    /**
     * Get the product review that owns the image.
     */
    public function productReview(): BelongsTo
    {
        return $this->belongsTo(ProductReview::class);
    }
}

