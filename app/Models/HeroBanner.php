<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroBanner extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'badge_text',
        'primary_button_text',
        'primary_button_link',
        'secondary_button_text',
        'secondary_button_link',
        'text_position',
        'overlay_opacity',
        'sort_order',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'overlay_opacity' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::deleted(function (HeroBanner $hero) {
            // Clean up image when deleted
            if ($hero->image) {
                Storage::disk('public')->delete($hero->image);
            }
        });
    }

    /**
     * Ensure only one active hero banner.
     */
    public function ensureSingleActive(): void
    {
        if ($this->status === 'active') {
            static::where('id', '!=', $this->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
        }
    }

    /**
     * Get the overlay opacity as a CSS decimal (0.0 - 1.0).
     */
    public function getOverlayOpacityDecimalAttribute(): float
    {
        return $this->overlay_opacity / 100;
    }

    /**
     * Get the CSS text alignment class.
     */
    public function getTextAlignmentClassAttribute(): string
    {
        return match ($this->text_position) {
            'left' => 'text-left items-start',
            'center' => 'text-center items-center',
            'right' => 'text-right items-end',
            default => 'text-center items-center',
        };
    }

    /**
     * Get the status label in Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order');
    }
}

