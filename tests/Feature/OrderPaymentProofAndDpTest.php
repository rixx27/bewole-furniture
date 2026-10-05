<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class OrderPaymentProofAndDpTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Product $product;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $category = Category::create([
            'name' => 'Kursi',
            'slug' => 'kursi',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Kursi Jati Mewah',
            'slug' => 'kursi-jati-mewah',
            'price' => 5000000,
            'status' => 'active',
            'stock' => 10,
        ]);

        $this->order = Order::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'order_code' => 'ORD-TEST-001',
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '08123456789',
            'shipping_address' => 'Jl. Pemuda No 10',
            'city' => 'Jepara',
            'quantity' => 1,
            'total_price' => 5000000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'down_payment_amount' => 0,
        ]);
    }

    /**
     * Test: Customer can upload payment proof for DP or full payment.
     */
    public function test_customer_can_upload_payment_proof(): void
    {
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\OrderPaymentUpload::class, [
                'orderId' => $this->order->id,
            ])
            ->set('proof', $file)
            ->call('uploadPaymentProof')
            ->assertHasNoErrors();

        $this->order->refresh();
        $this->assertNotNull($this->order->payment_proof);
        $this->assertEquals(PaymentStatus::Unpaid->value, $this->order->payment_status);
        $this->assertNotNull($this->order->payment_proof_uploaded_at);
        $this->assertTrue($this->order->has_payment_proof);
        Storage::disk('public')->assertExists($this->order->payment_proof);
    }

    /**
     * Test: Customer can upload final payment proof when in down_payment state.
     */
    public function test_customer_can_upload_final_payment_proof(): void
    {
        $this->order->update([
            'payment_status' => PaymentStatus::DownPayment->value,
            'down_payment_amount' => 2500000,
        ]);

        $file = UploadedFile::fake()->image('bukti_pelunasan.jpg');

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\OrderPaymentUpload::class, [
                'orderId' => $this->order->id,
                'isFinalPayment' => true,
            ])
            ->set('proof', $file)
            ->call('uploadPaymentProof')
            ->assertHasNoErrors();

        $this->order->refresh();
        $this->assertNotNull($this->order->final_payment_proof);
        $this->assertNotNull($this->order->final_payment_proof_uploaded_at);
        $this->assertTrue($this->order->has_final_payment_proof);
        Storage::disk('public')->assertExists($this->order->final_payment_proof);
    }

    /**
     * Test: Admin can verify payment as Down Payment with custom DP amount.
     */
    public function test_admin_can_verify_down_payment(): void
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Order\OrderPayment::class)
            ->call('loadOrder', $this->order->id)
            ->set('payment_status', 'down_payment')
            ->set('down_payment_amount', '2.000.000')
            ->set('notes', 'DP 2jt diterima via BCA')
            ->call('updatePayment')
            ->assertHasNoErrors();

        $this->order->refresh();
        $this->assertEquals(PaymentStatus::DownPayment->value, $this->order->payment_status);
        $this->assertEquals(2000000, (float) $this->order->down_payment_amount);
        $this->assertEquals(3000000, (float) $this->order->remaining_payment);
        $this->assertEquals('Rp 2.000.000', $this->order->formatted_down_payment_amount);
        $this->assertEquals('Rp 3.000.000', $this->order->formatted_remaining_payment);
    }

    /**
     * Test: Admin can verify full payment (Lunas).
     */
    public function test_admin_can_verify_full_payment(): void
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Order\OrderPayment::class)
            ->call('loadOrder', $this->order->id)
            ->set('payment_status', 'paid')
            ->call('updatePayment')
            ->assertHasNoErrors();

        $this->order->refresh();
        $this->assertEquals(PaymentStatus::Paid->value, $this->order->payment_status);
        $this->assertEquals(5000000, (float) $this->order->down_payment_amount);
        $this->assertEquals(0, (float) $this->order->remaining_payment);
        $this->assertEquals('Lunas', $this->order->payment_status_label);
    }

    /**
     * Test: Admin can reject payment proof with a reason.
     */
    public function test_admin_can_reject_payment_with_reason(): void
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Order\OrderPayment::class)
            ->call('loadOrder', $this->order->id)
            ->set('payment_status', 'failed')
            ->set('rejection_reason', 'Nominal transfer tidak sesuai dengan tagihan.')
            ->call('updatePayment')
            ->assertHasNoErrors();

        $this->order->refresh();
        $this->assertEquals(PaymentStatus::Failed->value, $this->order->payment_status);
        $this->assertEquals('Nominal transfer tidak sesuai dengan tagihan.', $this->order->payment_rejection_reason);
    }

    /**
     * Test: Complete multi-stage installment workflow (Pembayaran Ke-1 DP, Ke-2 Termin, Ke-3 Pelunasan).
     */
    public function test_multi_stage_installment_payments_from_dp_to_full_settlement(): void
    {
        // 1. Customer uploads Pembayaran Ke-1 (DP Awal)
        $file1 = UploadedFile::fake()->image('termin1_dp.jpg');
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\OrderPaymentUpload::class, ['orderId' => $this->order->id])
            ->set('proof', $file1)
            ->set('customer_notes', 'Transfer DP awal 2jt')
            ->call('uploadPaymentProof')
            ->assertHasNoErrors();

        $this->order->refresh();
        $this->assertCount(1, $this->order->payments);
        $payment1 = $this->order->payments->first();
        $this->assertEquals(1, $payment1->payment_number);
        $this->assertEquals('pending', $payment1->status);
        $this->assertEquals('Transfer DP awal 2jt', $payment1->customer_notes);

        // 2. Admin verifies Pembayaran Ke-1 (DP Awal Rp 2.000.000)
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Order\OrderPayment::class)
            ->call('loadOrder', $this->order->id)
            ->call('startVerifyInstallment', $payment1->id)
            ->set('verify_amount', '2.000.000')
            ->set('verify_notes', 'DP masuk rekening BCA')
            ->call('confirmVerifyInstallment')
            ->assertHasNoErrors();

        $this->order->refresh();
        $payment1->refresh();
        $this->assertEquals('verified', $payment1->status);
        $this->assertEquals(2000000, (float) $payment1->amount);
        $this->assertEquals(PaymentStatus::DownPayment->value, $this->order->payment_status);
        $this->assertEquals(2000000, (float) $this->order->down_payment_amount);
        $this->assertEquals(3000000, (float) $this->order->remaining_payment);

        // 3. Customer uploads Pembayaran Ke-2 (Termin Progres Rp 1.500.000)
        $file2 = UploadedFile::fake()->image('termin2_progres.jpg');
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\OrderPaymentUpload::class, ['orderId' => $this->order->id])
            ->set('proof', $file2)
            ->set('customer_notes', 'Transfer termin ke-2 progres finishing')
            ->call('uploadPaymentProof')
            ->assertHasNoErrors();

        $this->order->refresh();
        $this->assertCount(2, $this->order->payments);
        $payment2 = $this->order->payments->last();
        $this->assertEquals(2, $payment2->payment_number);
        $this->assertEquals('pending', $payment2->status);

        // 4. Admin verifies Pembayaran Ke-2 (Rp 1.500.000)
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Order\OrderPayment::class)
            ->call('loadOrder', $this->order->id)
            ->call('startVerifyInstallment', $payment2->id)
            ->set('verify_amount', '1.500.000')
            ->call('confirmVerifyInstallment')
            ->assertHasNoErrors();

        $this->order->refresh();
        $payment2->refresh();
        $this->assertEquals('verified', $payment2->status);
        $this->assertEquals(1500000, (float) $payment2->amount);
        $this->assertEquals(3500000, (float) $this->order->down_payment_amount);
        $this->assertEquals(1500000, (float) $this->order->remaining_payment);

        // 5. Customer uploads Pembayaran Ke-3 (Pelunasan sisa Rp 1.500.000)
        $file3 = UploadedFile::fake()->image('termin3_pelunasan.jpg');
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\OrderPaymentUpload::class, ['orderId' => $this->order->id])
            ->set('proof', $file3)
            ->set('customer_notes', 'Pelunasan selesai')
            ->call('uploadPaymentProof')
            ->assertHasNoErrors();

        $this->order->refresh();
        $this->assertCount(3, $this->order->payments);
        $payment3 = $this->order->payments->last();
        $this->assertEquals(3, $payment3->payment_number);

        // 6. Admin verifies Pembayaran Ke-3 (Pelunasan Rp 1.500.000)
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Order\OrderPayment::class)
            ->call('loadOrder', $this->order->id)
            ->call('startVerifyInstallment', $payment3->id)
            ->set('verify_amount', '1.500.000')
            ->call('confirmVerifyInstallment')
            ->assertHasNoErrors();

        $this->order->refresh();
        $payment3->refresh();
        $this->assertEquals('verified', $payment3->status);
        $this->assertEquals(PaymentStatus::Paid->value, $this->order->payment_status);
        $this->assertEquals(5000000, (float) $this->order->down_payment_amount);
        $this->assertEquals(0, (float) $this->order->remaining_payment);
        $this->assertEquals('Lunas', $this->order->payment_status_label);
    }

    /**
     * Test: Admin can add manual payment (e.g. Cash / WA direct).
     */
    public function test_admin_can_add_manual_payment(): void
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Order\OrderPayment::class)
            ->call('loadOrder', $this->order->id)
            ->set('manual_amount', '2.500.000')
            ->set('manual_method', 'cash')
            ->set('manual_notes', 'Diterima tunai di showroom')
            ->call('saveManualPayment')
            ->assertHasNoErrors();

        $this->order->refresh();
        $this->assertCount(1, $this->order->payments);
        $manualPayment = $this->order->payments->first();
        $this->assertEquals('verified', $manualPayment->status);
        $this->assertEquals('cash', $manualPayment->payment_method);
        $this->assertEquals(2500000, (float) $manualPayment->amount);
        $this->assertEquals('Diterima tunai di showroom', $manualPayment->admin_notes);
        $this->assertEquals(PaymentStatus::DownPayment->value, $this->order->payment_status);
        $this->assertEquals(2500000, (float) $this->order->remaining_payment);
    }

    /**
     * Test: Admin can reject a specific installment with a reason.
     */
    public function test_admin_can_reject_specific_installment(): void
    {
        $file = UploadedFile::fake()->image('bukti_salah.jpg');
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\OrderPaymentUpload::class, ['orderId' => $this->order->id])
            ->set('proof', $file)
            ->call('uploadPaymentProof')
            ->assertHasNoErrors();

        $this->order->refresh();
        $payment = $this->order->payments->first();

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Admin\Order\OrderPayment::class)
            ->call('loadOrder', $this->order->id)
            ->call('startRejectInstallment', $payment->id)
            ->set('reject_reason', 'Struk transfer tidak jelas dan buram')
            ->call('confirmRejectInstallment')
            ->assertHasNoErrors();

        $payment->refresh();
        $this->order->refresh();
        $this->assertEquals('rejected', $payment->status);
        $this->assertEquals('Struk transfer tidak jelas dan buram', $payment->rejection_reason);
        $this->assertEquals(PaymentStatus::Failed->value, $this->order->payment_status);
    }
}

