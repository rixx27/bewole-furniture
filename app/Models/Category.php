<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'slug',
        'cover_image',
        'short_description',
        'sort_order',
        'is_active',
        'show_on_home',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_on_home' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
            if (empty($category->code)) {
                $category->code = static::generateNextCode();
            }
        });

        static::updating(function (Category $category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::deleted(function (Category $category) {
            if ($category->cover_image) {
                Storage::disk('public')->delete($category->cover_image);
            }
        });
    }

    /**
     * Generate the next sequential category code (CAT001, CAT002, ...).
     */
    protected static function generateNextCode(): string
    {
        $last = static::max('id') ?? 0;

        return 'CAT' . str_pad((string) ((int) $last + 1), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the absolute URL for the cover image.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : null;
    }

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include categories shown on home.
     */
    public function scopeShowOnHome($query)
    {
        return $query->where('show_on_home', true);
    }

    /**
     * Scope a query to order categories by sort order then name.
     * Categories with specified order (1, 2, 3...) appear first,
     * while unprioritized categories (sort_order = 0 or null) appear after.
     */
    public function scopeSorted($query)
    {
        return $query
            ->orderByRaw('CASE WHEN sort_order = 0 OR sort_order IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }
}
