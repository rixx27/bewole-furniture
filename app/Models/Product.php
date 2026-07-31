<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'discount_percentage',
        'discount_price',
        'sku',
        'material',
        'dimensions',
        'weight',
        'thumbnail',
        'status',
        'is_featured',
        'stock',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'discount_price' => 'integer',
            'discount_percentage' => 'integer',
            'is_featured' => 'boolean',
            'stock' => 'integer',
        ];
    }

    /**
     * The valid status values for products.
     */
    public const STATUSES = ['active', 'pre_order', 'sold_out'];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            // Auto-calculate final price
            $product->calculateFinalPrice();
            // Auto-set status based on stock on creation
            $product->autoAdjustStatus();
        });

        static::updating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            // Auto-calculate final price
            $product->calculateFinalPrice();
            // Auto-adjust status only when stock changes and status is not explicitly set
            if ($product->isDirty('stock') && !$product->isDirty('status')) {
                $product->autoAdjustStatus();
            }
        });

        static::deleted(function (Product $product) {
            // Clean up thumbnail
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
        });
    }

    /**
     * Calculate the final price based on discount percentage.
     */
    public function calculateFinalPrice(): void
    {
        if ($this->discount_percentage && $this->discount_percentage > 0) {
            $this->discount_price = (int) round($this->price - ($this->price * $this->discount_percentage / 100));
        } else {
            $this->discount_price = $this->price;
            $this->discount_percentage = null;
        }
    }

    /**
     * Auto-adjust status based on stock quantity.
     *
     * Rules:
     * - If stock is 0 and status is NOT 'sold_out', change to 'pre_order'
     * - If stock > 0 and status is 'pre_order', change to 'active'
     * - 'sold_out' status is NEVER changed automatically
     */
    public function autoAdjustStatus(): void
    {
        // Never auto-change if status is sold_out
        if ($this->status === 'sold_out') {
            return;
        }

        if ($this->stock <= 0) {
            $this->status = 'pre_order';
        } elseif ($this->stock > 0 && $this->status === 'pre_order') {
            $this->status = 'active';
        }
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the gallery images (non-primary).
     */
    public function galleryImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', false)->orderBy('sort_order');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the orders for the product.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the visible reviews for the product.
     */
    public function visibleReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('is_visible', true);
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get the formatted discount price attribute.
     */
    public function getFormattedDiscountPriceAttribute(): string
    {
        return $this->discount_price ? 'Rp ' . number_format($this->discount_price, 0, ',', '.') : '';
    }

    /**
     * Get the discount percentage attribute.
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->discount_price && $this->price > 0 && $this->price != $this->discount_price) {
            return (int) round((1 - $this->discount_price / $this->price) * 100);
        }

        return $this->attributes['discount_percentage'] ?? null;
    }

    /**
     * Check if product has discount.
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_percentage && $this->discount_percentage > 0;
    }

    /**
     * Get status label in Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'pre_order' => 'Pre Order',
            'sold_out' => 'Habis Terjual',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color class.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'emerald',
            'pre_order' => 'amber',
            'sold_out' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

