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
}
