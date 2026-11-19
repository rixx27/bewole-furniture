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
        'price_usd',
        'price_matang',
        'price_matang_usd',
        'show_price',
        'packing_fee',
        'discount_percentage',
        'discount_price',
        'discount_price_usd',
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
            'price_usd' => 'decimal:2',
            'price_matang' => 'integer',
            'price_matang_usd' => 'decimal:2',
            'show_price' => 'boolean',
            'packing_fee' => 'integer',
            'discount_price' => 'integer',
            'discount_price_usd' => 'decimal:2',
            'discount_percentage' => 'integer',
            'is_featured' => 'boolean',
            'stock' => 'integer',
            'sort_order' => 'integer',
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
            // Auto-sync IDR and USD prices
            $product->syncCurrencyPrices();
            // Auto-calculate final price
            $product->calculateFinalPrice();
            // Auto-set status based on stock on creation
            $product->autoAdjustStatus();
            // Auto-shift sort order if set
            app(\App\Services\ProductSortOrderService::class)->adjustOnCreating($product);
        });

        static::updating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            // Auto-sync IDR and USD prices
            $product->syncCurrencyPrices();
            // Auto-calculate final price
            $product->calculateFinalPrice();
            // Auto-adjust status only when stock changes and status is not explicitly set
            if ($product->isDirty('stock') && !$product->isDirty('status')) {
                $product->autoAdjustStatus();
            }
            // Auto-shift sort order if changed
            if ($product->isDirty('sort_order') || $product->isDirty('category_id')) {
                app(\App\Services\ProductSortOrderService::class)->adjustOnUpdating($product);
            }
        });

        static::deleted(function (Product $product) {
            // Clean up thumbnail
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            // Reorder remaining products in the same category
            app(\App\Services\ProductSortOrderService::class)->adjustOnDeleted($product);
        });
    }

    /**
     * Synchronize IDR and USD prices if one is provided and the other is empty.
     */
    public function syncCurrencyPrices(): void
    {
        $currencyService = app(\App\Services\CurrencyService::class);
        $rate = $currencyService->getUsdRate();

        $priceIdr = isset($this->attributes['price']) && $this->attributes['price'] !== '' ? (int) $this->attributes['price'] : 0;
        $priceUsd = isset($this->attributes['price_usd']) && $this->attributes['price_usd'] !== '' && $this->attributes['price_usd'] !== null ? (float) $this->attributes['price_usd'] : 0;

        if ($priceUsd > 0 && $priceIdr <= 0) {
            $this->attributes['price'] = $currencyService->toIdr($priceUsd, $rate);
        } elseif ($priceIdr > 0 && $priceUsd <= 0) {
            $this->attributes['price_usd'] = $currencyService->toUsd($priceIdr, $rate);
        }

        // Keep price_matang and price_matang_usd in sync for backward compatibility
        $this->attributes['price_matang'] = $this->attributes['price'] ?? 0;
        $this->attributes['price_matang_usd'] = $this->attributes['price_usd'] ?? 0;
    }

    /**
     * Calculate the final price based on discount percentage or discount price.
     */
    public function calculateFinalPrice(): void
    {
        $price = (int) ($this->attributes['price'] ?? 0);
        $priceUsd = isset($this->attributes['price_usd']) && $this->attributes['price_usd'] !== null ? (float) $this->attributes['price_usd'] : 0;

        // Check if discount_percentage was explicitly set in attributes
        if (array_key_exists('discount_percentage', $this->attributes)) {
            $percentage = $this->attributes['discount_percentage'];

            if ($percentage !== null && is_numeric($percentage) && (int) $percentage > 0 && $price > 0) {
                $validPercentage = min(100, max(0, (int) $percentage));
                $this->attributes['discount_percentage'] = $validPercentage;
                $this->attributes['discount_price'] = (int) round($price - ($price * $validPercentage / 100));
                if ($priceUsd > 0) {
                    $this->attributes['discount_price_usd'] = round($priceUsd - ($priceUsd * $validPercentage / 100), 2);
                } else {
                    $this->attributes['discount_price_usd'] = null;
                }
            } else {
                $this->attributes['discount_percentage'] = null;
                $this->attributes['discount_price'] = null;
                $this->attributes['discount_price_usd'] = null;
            }
        } elseif (!empty($this->attributes['discount_price']) && $price > 0) {
            // Fallback: if discount_price was explicitly provided (e.g. in test seeders) without discount_percentage
            $discountPrice = (int) $this->attributes['discount_price'];
            if ($discountPrice > 0 && $discountPrice < $price) {
                $this->attributes['discount_price'] = $discountPrice;
                $percentage = (int) round((1 - $discountPrice / $price) * 100);
                $this->attributes['discount_percentage'] = $percentage;
                if ($priceUsd > 0) {
                    $this->attributes['discount_price_usd'] = round($priceUsd - ($priceUsd * $percentage / 100), 2);
                } else {
                    $this->attributes['discount_price_usd'] = null;
                }
            } else {
                $this->attributes['discount_price'] = null;
                $this->attributes['discount_price_usd'] = null;
                $this->attributes['discount_percentage'] = null;
            }
        } else {
            $this->attributes['discount_percentage'] = null;
            $this->attributes['discount_price'] = null;
            $this->attributes['discount_price_usd'] = null;
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
     * Get the formatted price in IDR.
     */
    public function getFormattedPriceIdrAttribute(): string
    {
        return 'Rp ' . number_format($this->price ?? 0, 0, ',', '.');
    }

    /**
     * Get the formatted price in USD.
     */
    public function getFormattedPriceUsdAttribute(): string
    {
        $usd = $this->price_usd !== null ? (float) $this->price_usd : \App\Helpers\Currency::toUsd((float) ($this->price ?? 0));
        return '$ ' . number_format($usd, 2, '.', ',');
    }

    /**
     * Get the formatted price attribute (default / mentah, respects active currency: USD or IDR).
     */
    public function getFormattedPriceAttribute(): string
    {
        if (\App\Helpers\Currency::isUsd()) {
            return $this->formatted_price_usd;
        }
        return $this->formatted_price_idr;
    }

    /**
     * Get the formatted raw / unfinished unit price attribute.
     */
    public function getFormattedPriceUnfinishedAttribute(): string
    {
        return $this->formatted_price;
    }

    public function getFormattedPriceMentahAttribute(): string
    {
        return $this->formatted_price_unfinished;
    }

    /**
     * Get the formatted finished unit price in IDR.
     */
    public function getFormattedPriceFinishedIdrAttribute(): string
    {
        return $this->formatted_price_idr;
    }

    /**
     * Get the formatted finished unit price in USD.
     */
    public function getFormattedPriceFinishedUsdAttribute(): string
    {
        return $this->formatted_price_usd;
    }

    /**
     * Get the formatted finished unit price attribute (respects active currency).
     */
    public function getFormattedPriceFinishedAttribute(): string
    {
        return $this->formatted_price;
    }

    public function getFormattedPriceMatangAttribute(): string
    {
        return $this->formatted_price;
    }

    /**
     * Get the formatted packing fee attribute.
     */
    public function getFormattedPackingFeeAttribute(): string
    {
        if (\App\Helpers\Currency::isUsd()) {
            $feeUsd = \App\Helpers\Currency::toUsd((float) ($this->packing_fee ?? 0));
            return '$ ' . number_format($feeUsd, 2, '.', ',');
        }
        return 'Rp ' . number_format($this->packing_fee ?? 0, 0, ',', '.');
    }

    /**
     * Get the effective price for finished unit.
     */
    public function getFinishedPriceAttribute(): int
    {
        return (int) $this->price;
    }

    public function getMatangPriceAttribute(): int
    {
        return (int) $this->price;
    }

    /**
     * Get the effective price for unfinished unit.
     */
    public function getUnfinishedPriceAttribute(): int
    {
        return (int) $this->price;
    }

    public function getMentahPriceAttribute(): int
    {
        return (int) $this->price;
    }

    /**
     * Get the formatted discount price attribute (respects active currency).
     */
    public function getFormattedDiscountPriceAttribute(): string
    {
        if (!$this->has_discount || empty($this->discount_price)) {
            return '';
        }

        if (\App\Helpers\Currency::isUsd()) {
            $usd = $this->discount_price_usd ?: \App\Helpers\Currency::toUsd((float) $this->discount_price);
            return '$ ' . number_format($usd, 2, '.', ',');
        }

        return 'Rp ' . number_format($this->discount_price, 0, ',', '.');
    }

    /**
     * Get the discount percentage attribute.
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if (array_key_exists('discount_percentage', $this->attributes)) {
            $val = $this->attributes['discount_percentage'];
            return ($val !== null && is_numeric($val) && (int) $val > 0) ? (int) $val : null;
        }

        if (!empty($this->attributes['discount_price']) && !empty($this->attributes['price'])) {
            $price = (int) $this->attributes['price'];
            $discountPrice = (int) $this->attributes['discount_price'];
            if ($price > 0 && $discountPrice > 0 && $discountPrice < $price) {
                return (int) round((1 - $discountPrice / $price) * 100);
            }
        }

        return null;
    }

    /**
     * Check if product has discount.
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_percentage !== null && $this->discount_percentage > 0;
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
     * Scope a query to order by sort order then name.
     * Products with specified order (1, 2, 3...) appear first,
     * while unprioritized products (sort_order = 0 or null) appear after.
     */
    public function scopeSorted($query)
    {
        return $query
            ->orderByRaw('CASE WHEN sort_order = 0 OR sort_order IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
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

