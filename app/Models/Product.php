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
        'price_matang',
        'packing_fee',
        'discount_percentage',
        'discount_price',
        'sku',
        'material',
        'seat_material_usage',
        'packing_material_usage',
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
            'price_matang' => 'integer',
            'packing_fee' => 'integer',
            'discount_price' => 'integer',
            'discount_percentage' => 'integer',
            'is_featured' => 'boolean',
            'stock' => 'integer',
            'weight' => 'float',
            'seat_material_usage' => 'float',
            'packing_material_usage' => 'float',
        ];
    }

    /**
     * Mutator to ensure weight is always stored as a clean float or null.
     */
    public function setWeightAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['weight'] = null;
        } else {
            $cleaned = preg_replace('/\s*kg\b/i', '', (string) $value);
            $cleaned = str_replace(',', '.', trim($cleaned));
            $this->attributes['weight'] = is_numeric($cleaned) ? (float) $cleaned : null;
        }
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
     * Auto-adjust status (defaults to active / Tersedia).
     */
    public function autoAdjustStatus(): void
    {
        if (empty($this->status)) {
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
     * Get the formatted price attribute (default / mentah).
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get the formatted raw / unfinished unit price attribute.
     */
    public function getFormattedPriceUnfinishedAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedPriceMentahAttribute(): string
    {
        return $this->formatted_price_unfinished;
    }

    /**
     * Get the formatted finished unit price attribute.
     */
    public function getFormattedPriceFinishedAttribute(): string
    {
        $finishedPrice = $this->price_matang ?: $this->price;
        return 'Rp ' . number_format($finishedPrice, 0, ',', '.');
    }

    public function getFormattedPriceMatangAttribute(): string
    {
        return $this->formatted_price_finished;
    }

    /**
     * Get the formatted packing fee attribute.
     */
    public function getFormattedPackingFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->packing_fee ?? 0, 0, ',', '.');
    }

    /**
     * Get the effective price for finished unit.
     */
    public function getFinishedPriceAttribute(): int
    {
        return $this->price_matang ?: $this->price;
    }

    public function getMatangPriceAttribute(): int
    {
        return $this->finished_price;
    }

    /**
     * Get the effective price for unfinished unit.
     */
    public function getUnfinishedPriceAttribute(): int
    {
        return $this->price;
    }

    public function getMentahPriceAttribute(): int
    {
        return $this->unfinished_price;
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
            'sold_out' => 'Habis Terjual',
            default => 'Tersedia',
        };
    }

    /**
     * Get status color class.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'sold_out' => 'gray',
            default => 'emerald',
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

    /**
     * Get all custom material options for this product.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(ProductMaterial::class);
    }

    /**
     * Get seat materials for this product.
     */
    public function seatMaterials(): HasMany
    {
        return $this->hasMany(ProductMaterial::class)->where('type', 'seat_material');
    }

    /**
     * Get packing materials for this product.
     */
    public function packingMaterials(): HasMany
    {
        return $this->hasMany(ProductMaterial::class)->where('type', 'packing_material');
    }

    /**
     * Get active seat materials (product-specific or global defaults).
     */
    public function getAvailableSeatMaterials()
    {
        $specific = $this->seatMaterials()->where('is_active', true)->get();
        if ($specific->isNotEmpty()) {
            return $specific;
        }

        $global = ProductMaterial::whereNull('product_id')->seatMaterial()->active()->get();
        if ($global->isNotEmpty()) {
            return $global;
        }

        return collect([
            new ProductMaterial(['name' => 'Kulit', 'price_per_meter' => 25000, 'is_active' => true]),
            new ProductMaterial(['name' => 'Benang', 'price_per_meter' => 5000, 'is_active' => true]),
            new ProductMaterial(['name' => 'Anyaman', 'price_per_meter' => 15000, 'is_active' => true]),
        ]);
    }

    /**
     * Get active packing materials (product-specific or global defaults).
     */
    public function getAvailablePackingMaterials()
    {
        $specific = $this->packingMaterials()->where('is_active', true)->get();
        if ($specific->isNotEmpty()) {
            return $specific;
        }

        $global = ProductMaterial::whereNull('product_id')->packingMaterial()->active()->get();
        if ($global->isNotEmpty()) {
            return $global;
        }

        return collect([
            new ProductMaterial(['name' => 'Kardus', 'price_per_meter' => 10000, 'is_active' => true]),
            new ProductMaterial(['name' => 'Plastik', 'price_per_meter' => 5000, 'is_active' => true]),
        ]);
    }

    /**
     * Get customization options definition for this product.
     */
    public function getCustomizationOptions(): ?array
    {
        return null;
    }
}

