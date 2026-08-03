<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyStatistic extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_statistics';

    /**
     * Statistic types.
     */
    public const TYPE_AUTO = 'auto';
    public const TYPE_MANUAL = 'manual';

    /**
     * Available automatic data sources.
     */
    public const SOURCES = [
        'products' => 'Products',
        'orders' => 'Orders',
        'users' => 'Users',
        'reviews' => 'Reviews',
        'categories' => 'Categories',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_profile_id',
        'icon',
        'title',
        'type',
        'source',
        'manual_value',
        'sort_order',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the company profile that owns the statistic.
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    /**
     * Get the statistic type label in Bahasa Indonesia.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === self::TYPE_MANUAL ? 'Manual' : 'Otomatis';
    }

    /**
     * Scope a query to only include active statistics.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
