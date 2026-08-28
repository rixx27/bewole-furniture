<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'meubel_type',
        'customization_option',
        'packing_type',
        'customization_price',
        'packing_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'customization_price' => 'decimal:2',
            'packing_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getMeubelTypeLabelAttribute(): string
    {
        return match ($this->meubel_type) {
            'mentah', 'raw' => 'Meubel Mentah',
            'matang', 'finished' => 'Meubel Matang',
            default => $this->meubel_type ?: '-',
        };
    }

    public function getPackingTypeLabelAttribute(): string
    {
        return match ($this->packing_type) {
            'kardus', 'cardboard' => 'Kardus',
            'plastik', 'plastic' => 'Plastik',
            default => $this->packing_type ?: '-',
        };
    }
}
