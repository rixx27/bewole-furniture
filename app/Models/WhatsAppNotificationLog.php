<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppNotificationLog extends Model
{
    protected $table = 'whatsapp_notification_logs';

    protected $fillable = [
        'order_id',
        'order_code',
        'recipient_phone',
        'status_trigger',
        'message',
        'status',
        'error_message',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
