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
        'quantity',
        'total_price',
        'notes',
        'status',
        'payment_status',
        'payment_method',
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
            'shipping_date' => 'date',
            'pickup_date' => 'date',
        ];
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
     * Get the review associated with the order.
     */
    public function review(): HasOne
    {
        return $this->hasOne(ProductReview::class);
    }

    /**
     * Get the formatted total price attribute.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
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

