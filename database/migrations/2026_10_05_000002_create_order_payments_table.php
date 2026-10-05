<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->unsignedInteger('payment_number')->default(1);
            $table->string('title')->nullable(); // e.g. "Pembayaran Ke-1 (DP Awal)", "Pembayaran Ke-2"
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('proof_file')->nullable();
            $table->string('payment_method', 50)->default('bank_transfer'); // bank_transfer, cash, qris, etc.
            $table->string('status', 50)->default('pending'); // pending, verified, rejected
            $table->text('rejection_reason')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'payment_number']);
            $table->index(['order_id', 'status']);
        });

        // Backfill existing payments from orders table for full backward compatibility
        try {
            $orders = DB::table('orders')->get();
            foreach ($orders as $order) {
                // If order has initial payment proof or recorded DP amount
                if (!empty($order->payment_proof) || (float) ($order->down_payment_amount ?? 0) > 0) {
                    $status = 'pending';
                    if ($order->payment_status === 'failed') {
                        $status = 'rejected';
                    } elseif (in_array($order->payment_status, ['down_payment', 'paid'])) {
                        $status = 'verified';
                    }

                    $dpAmount = (float) ($order->down_payment_amount ?? 0);
                    // If order is paid and had no final proof, the first payment might be full payment
                    if ($order->payment_status === 'paid' && empty($order->final_payment_proof)) {
                        $dpAmount = (float) $order->total_price;
                    }

                    DB::table('order_payments')->insert([
                        'order_id' => $order->id,
                        'payment_number' => 1,
                        'title' => 'Pembayaran Ke-1 (DP Awal)',
                        'amount' => $dpAmount,
                        'proof_file' => $order->payment_proof,
                        'payment_method' => $order->payment_method ?: 'bank_transfer',
                        'status' => $status,
                        'rejection_reason' => $order->payment_rejection_reason,
                        'customer_notes' => null,
                        'admin_notes' => 'Migrasi otomatis data pembayaran',
                        'verified_by' => null,
                        'verified_at' => $status === 'verified' ? ($order->payment_proof_uploaded_at ?? $order->updated_at) : null,
                        'created_at' => $order->payment_proof_uploaded_at ?? $order->created_at,
                        'updated_at' => $order->updated_at ?? now(),
                    ]);
                }

                // If order also has final payment proof
                if (!empty($order->final_payment_proof)) {
                    $finalStatus = $order->payment_status === 'paid' ? 'verified' : 'pending';
                    $finalAmount = max(0, (float) $order->total_price - (float) ($order->down_payment_amount ?? 0));

                    DB::table('order_payments')->insert([
                        'order_id' => $order->id,
                        'payment_number' => 2,
                        'title' => 'Pembayaran Ke-2 (Pelunasan)',
                        'amount' => $finalAmount,
                        'proof_file' => $order->final_payment_proof,
                        'payment_method' => $order->payment_method ?: 'bank_transfer',
                        'status' => $finalStatus,
                        'rejection_reason' => null,
                        'customer_notes' => null,
                        'admin_notes' => 'Migrasi otomatis data pelunasan',
                        'verified_by' => null,
                        'verified_at' => $finalStatus === 'verified' ? ($order->final_payment_proof_uploaded_at ?? $order->updated_at) : null,
                        'created_at' => $order->final_payment_proof_uploaded_at ?? $order->updated_at,
                        'updated_at' => $order->updated_at ?? now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Log error or ignore if running in test environment with fresh tables
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
