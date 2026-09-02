<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('down_payment_amount', 12, 2)->default(0)->after('payment_method');
            $table->string('payment_proof')->nullable()->after('down_payment_amount');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof');
            $table->string('final_payment_proof')->nullable()->after('payment_proof_uploaded_at');
            $table->timestamp('final_payment_proof_uploaded_at')->nullable()->after('final_payment_proof');
            $table->text('payment_rejection_reason')->nullable()->after('final_payment_proof_uploaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'down_payment_amount',
                'payment_proof',
                'payment_proof_uploaded_at',
                'final_payment_proof',
                'final_payment_proof_uploaded_at',
                'payment_rejection_reason',
            ]);
        });
    }
};
