<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_number',
        'title',
        'amount',
        'proof_file',
        'payment_method',
        'status',
        'rejection_reason',
        'customer_notes',
        'admin_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
        'payment_number' => 'integer',
    ];

    /**
     * Get the order that owns this payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the admin who verified this payment.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get URL of payment proof file.
     */
    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_file ? asset('storage/' . $this->proof_file) : null;
    }

    /**
     * Get formatted amount attribute.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->amount ?? 0), 0, ',', '.');
    }

    /**
     * Get user-friendly status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            'pending' => 'Menunggu Verifikasi',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status theme color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'verified' => 'emerald',
            'rejected' => 'rose',
            'pending' => 'amber',
            default => 'gray',
        };
    }

    /**
     * Get user-friendly payment method label.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'bank_transfer', 'transfer' => 'Transfer Bank',
            'cash', 'cod' => 'Tunai / Cash',
            'qris' => 'QRIS',
            default => $this->payment_method ? ucwords(str_replace('_', ' ', $this->payment_method)) : '-',
        };
    }

    /**
     * Check if payment has proof file.
     */
    public function getHasProofAttribute(): bool
    {
        return !empty($this->proof_file);
    }
}
