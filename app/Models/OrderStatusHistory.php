<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\Facades\Storage;

class OrderStatusHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'status',
        'description',
        'photo',
        'latitude',
        'longitude',
        'changed_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the photo full URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return asset('storage/' . $this->photo);
    }

    /**
     * Get the download URL for the progress photo.
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return route('orders.progress-photo.download', $this->id);
    }

    /**
     * Get the view URL for the full progress photo viewer page.
     */
    public function getViewUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return route('orders.progress-photo.view', $this->id);
    }

    /**
     * Get the order that owns the status history.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who changed the status.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
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
}

