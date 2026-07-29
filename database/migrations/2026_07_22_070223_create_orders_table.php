<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('orders', function (Blueprint $table) {

    $table->id();

    $table->foreignId('user_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->foreignId('product_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('order_code')->unique();

    $table->string('customer_name');

    $table->string('customer_phone');

    $table->string('customer_email')->nullable();

    $table->text('shipping_address');

    $table->string('city');

    $table->string('postal_code')->nullable();

    $table->unsignedInteger('quantity')->default(1);

    $table->decimal('total_price',12,2);

    $table->text('notes')->nullable();

    $table->enum('status',[
        'pending',
        'confirmed',
        'processing',
        'shipping',
        'completed',
        'cancelled'
    ])->default('pending');

    $table->enum('payment_status',[
        'unpaid',
        'paid',
        'failed',
        'refunded'
    ])->default('unpaid');

    $table->enum('payment_method',[
        'cash',
        'transfer',
        'qris'
    ])->nullable();

    $table->string('whatsapp_number')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}

return new CreateOrdersTable;

