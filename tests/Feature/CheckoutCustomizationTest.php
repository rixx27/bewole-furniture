<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutCustomizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $categoryKursi;
    protected Category $categoryMeja;
    protected Product $productKursi;
    protected Product $productMeja;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->categoryKursi = Category::create([
            'name' => 'Kursi',
            'slug' => 'kursi',
        ]);

        $this->categoryMeja = Category::create([
            'name' => 'Meja',
            'slug' => 'meja',
        ]);

        $this->productKursi = Product::create([
            'category_id' => $this->categoryKursi->id,
            'name' => 'Kursi Tamu Minimalis',
            'slug' => 'kursi-tamu-minimalis',
            'price' => 1780000,
            'price_matang' => 1780000,
            'packing_fee' => 0,
            'status' => 'active',
            'stock' => 10,
        ]);

        $this->productMeja = Product::create([
            'category_id' => $this->categoryMeja->id,
            'name' => 'Meja Makan Jati',
            'slug' => 'meja-makan-jati',
            'price' => 3600000,
            'price_matang' => 3600000,
            'packing_fee' => 0,
            'status' => 'active',
            'stock' => 5,
        ]);
    }

    /**
     * TEST 1: Checkout success with single finished product price.
     */
    public function test_standard_checkout_success(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 5);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('customer_name', 'Budi Santoso')
            ->set('customer_phone', '08123456789')
            ->set('province', 'Jawa Tengah')
            ->set('city', 'Jepara')
            ->set('shipping_address', 'Jl. Pemuda No 10')
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'product_id' => $this->productKursi->id,
            'quantity' => 5,
            'total_price' => 8900000, // 5 x 1.780.000
            'meubel_type' => 'finished',
            'customization_fee' => 0,
        ]);
    }

    /**
     * TEST 2: Backward compatibility of Order model for historical meubel labels.
     */
    public function test_order_model_backward_compatibility_labels(): void
    {
        $orderFinished = Order::create([
            'order_code' => 'BWL-TEST-001',
            'product_id' => $this->productKursi->id,
            'customer_name' => 'Test Finished',
            'customer_phone' => '08123456789',
            'shipping_address' => 'Jepara',
            'city' => 'Jepara',
            'meubel_type' => 'finished',
            'quantity' => 1,
            'total_price' => 1780000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'manual_transfer',
        ]);

        $orderMentah = Order::create([
            'order_code' => 'BWL-TEST-002',
            'product_id' => $this->productKursi->id,
            'customer_name' => 'Test Mentah',
            'customer_phone' => '08123456789',
            'shipping_address' => 'Jepara',
            'city' => 'Jepara',
            'meubel_type' => 'unfinished',
            'quantity' => 1,
            'total_price' => 1760000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'manual_transfer',
        ]);

        $this->assertEquals('Finished', $orderFinished->meubel_type_label);
        $this->assertEquals('Unfinished', $orderMentah->meubel_type_label);
    }

    /**
     * TEST 3: Multiple products in cart checkout correctly.
     */
    public function test_multiple_products_cart_checkout(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 2); // 2 x 1.780.000 = 3.560.000
        $cartService->add($this->productMeja->id, 1);  // 1 x 3.600.000 = 3.600.000

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('customer_name', 'Budi Santoso')
            ->set('customer_phone', '08123456789')
            ->set('province', 'Jawa Tengah')
            ->set('city', 'Jepara')
            ->set('shipping_address', 'Jl. Pemuda No 10')
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertRedirect();

        $order = Order::latest()->first();
        $this->assertEquals('finished', $order->meubel_type);
        $this->assertEquals(7160000, $order->total_price); // 3.560.000 + 3.600.000
        $this->assertEquals(0, $order->customization_fee);
    }
}

