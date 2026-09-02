<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'order_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_address',
        'city',
        'postal_code',
        'meubel_type',
        'packing_type',
        'customization_details',
        'customization_fee',
        'packing_fee',
        'quantity',
        'total_price',
        'notes',
        'status',
        'payment_status',
        'payment_method',
        'down_payment_amount',
        'payment_proof',
        'payment_proof_uploaded_at',
        'final_payment_proof',
        'final_payment_proof_uploaded_at',
        'payment_rejection_reason',
        'whatsapp_number',
        'shipping_method',
        'courier',
        'tracking_number',
        'driver_name',
        'vehicle_number',
        'shipping_date',
        'pickup_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'total_price' => 'decimal:2',
            'customization_details' => 'array',
            'customization_fee' => 'decimal:2',
            'packing_fee' => 'decimal:2',
            'down_payment_amount' => 'decimal:2',
            'payment_proof_uploaded_at' => 'datetime',
            'final_payment_proof_uploaded_at' => 'datetime',
            'shipping_date' => 'date',
            'pickup_date' => 'date',
        ];
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product associated with the order.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the status histories for the order.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest status history.
     */
    public function latestStatus()
    {
        return $this->hasOne(OrderStatusHistory::class)->latestOfMany();
    }

    /**
     * Get the review associated with the order (single/first review).
     */
    public function review(): HasOne
    {
        return $this->hasOne(ProductReview::class);
    }

    /**
     * Get all reviews associated with the order.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the formatted total price attribute.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Get the meubel type label attribute.
     */
    public function getMeubelTypeLabelAttribute(): string
    {
        return match ($this->meubel_type) {
            'mentah', 'raw', 'unfinished' => 'Unfinished',
            'matang', 'finished' => 'Finished',
            default => $this->meubel_type ?: '-',
        };
    }

    /**
     * Get the packing type label attribute.
     */
    public function getPackingTypeLabelAttribute(): string
    {
        return match ($this->packing_type) {
            'kardus', 'cardboard' => 'Kardus',
            'plastik', 'plastic' => 'Plastik',
            default => $this->packing_type ?: '-',
        };
    }

    /**
     * Get the payment method label attribute.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'manual_transfer', 'transfer' => 'Transfer Bank Manual',
            'cash', 'cod' => 'Tunai / COD',
            'qris' => 'QRIS',
            default => $this->payment_method ? ucwords(str_replace('_', ' ', $this->payment_method)) : '-',
        };
    }

    /**
     * Get the shipping method label attribute.
     */
    public function getShippingMethodLabelAttribute(): string
    {
        $method = ShippingMethod::tryFrom($this->shipping_method);
        return $method ? $method->label() : '-';
    }

    /**
     * Get the status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        $status = OrderStatus::tryFrom($this->status);
        return $status ? $status->label() : $this->status;
    }

    /**
     * Get the status color attribute.
     */
    public function getStatusColorAttribute(): string
    {
        $status = OrderStatus::tryFrom($this->status);
        return $status ? $status->color() : 'gray';
    }

    /**
     * Get the status emoji attribute.
     */
    public function getStatusEmojiAttribute(): string
    {
        $status = OrderStatus::tryFrom($this->status);
        return $status ? $status->emoji() : '⚙️';
    }

    /**
     * Get the payment status label attribute.
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        $status = PaymentStatus::tryFrom($this->payment_status);
        return $status ? $status->label() : $this->payment_status;
    }

    /**
     * Get the payment status color attribute.
     */
    public function getPaymentStatusColorAttribute(): string
    {
        $status = PaymentStatus::tryFrom($this->payment_status);
        return $status ? $status->color() : 'gray';
    }

    /**
     * Get the formatted down payment amount.
     */
    public function getFormattedDownPaymentAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->down_payment_amount ?? 0), 0, ',', '.');
    }

    /**
     * Get remaining payment amount.
     */
    public function getRemainingPaymentAttribute(): float
    {
        return (float) max(0, (float) $this->total_price - (float) ($this->down_payment_amount ?? 0));
    }

    /**
     * Get formatted remaining payment.
     */
    public function getFormattedRemainingPaymentAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_payment, 0, ',', '.');
    }

    /**
     * Get URL of initial payment proof.
     */
    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof ? asset('storage/' . $this->payment_proof) : null;
    }

    /**
     * Get URL of final payment proof.
     */
    public function getFinalPaymentProofUrlAttribute(): ?string
    {
        return $this->final_payment_proof ? asset('storage/' . $this->final_payment_proof) : null;
    }

    /**
     * Check if payment proof is available.
     */
    public function getHasPaymentProofAttribute(): bool
    {
        return !empty($this->payment_proof);
    }

    /**
     * Check if final payment proof is available.
     */
    public function getHasFinalPaymentProofAttribute(): bool
    {
        return !empty($this->final_payment_proof);
    }

    /**
     * Get the subtotal (total price before shipping).
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->total_price;
    }

    /**
     * Scope a query to only include orders with a specific status.
     */
    public function scopeWhereStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}

