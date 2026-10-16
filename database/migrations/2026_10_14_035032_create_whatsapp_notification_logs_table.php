<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('order_code');
            $table->string('recipient_phone');
            $table->string('status_trigger'); // payment_received, in_production, etc.
            $table->text('message');
            $table->string('status'); // sent, failed
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Index dan idempotensi: 1 pesanan hanya mengirim 1 notifikasi untuk status yang sama
            $table->unique(['order_id', 'status_trigger'], 'unique_order_status_notif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_notification_logs');
    }
};