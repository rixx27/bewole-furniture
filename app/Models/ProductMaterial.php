<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMaterial extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'name',
        'price_per_meter',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_meter' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSeatMaterial($query)
    {
        return $query->where('type', 'seat_material');
    }

    public function scopePackingMaterial($query)
    {
        return $query->where('type', 'packing_material');
    }
}
