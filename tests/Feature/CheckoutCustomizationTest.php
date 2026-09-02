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
            'price' => 1760000,
            'price_matang' => 1780000,
            'packing_fee' => 0,
            'status' => 'active',
            'stock' => 10,
        ]);

        $this->productMeja = Product::create([
            'category_id' => $this->categoryMeja->id,
            'name' => 'Meja Makan Jati',
            'slug' => 'meja-makan-jati',
            'price' => 3500000,
            'price_matang' => 3600000,
            'packing_fee' => 0,
            'status' => 'active',
            'stock' => 5,
        ]);
    }

    /**
     * TEST 1: Meubel Mentah checkout success.
     */
    public function test_meubel_mentah_checkout_success(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 5);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('meubel_type', 'mentah')
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
            'total_price' => 8800000, // 5 x 1.760.000
            'meubel_type' => 'mentah',
        ]);
    }

    /**
     * TEST 2: Meubel Matang checkout success with matang difference added.
     */
    public function test_meubel_matang_checkout_success(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 5);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('meubel_type', 'matang')
            ->set('customer_name', 'Budi Santoso')
            ->set('customer_phone', '08123456789')
            ->set('province', 'Jawa Tengah')
            ->set('city', 'Jepara')
            ->set('shipping_address', 'Jl. Pemuda No 10')
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertRedirect();

        $order = Order::latest()->first();
        $this->assertEquals('matang', $order->meubel_type);
        // Base: 5 x 1.760.000 = 8.800.000
        // Matang diff: 5 x (1.780.000 - 1.760.000) = 100.000
        // Total: 8.900.000 (5 x 1.780.000)
        $this->assertEquals(8900000, $order->total_price);
        $this->assertEquals(100000, $order->customization_fee);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->productKursi->id,
            'meubel_type' => 'matang',
        ]);
    }

    /**
     * TEST 3: Missing meubel_type fails validation.
     */
    public function test_missing_meubel_type_fails(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 1);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('customer_name', 'Budi Santoso')
            ->set('customer_phone', '08123456789')
            ->set('province', 'Jawa Tengah')
            ->set('city', 'Jepara')
            ->set('shipping_address', 'Jl. Pemuda No 10')
            ->call('placeOrder')
            ->assertHasErrors(['meubel_type']);
    }

    /**
     * TEST 4: Multiple products in cart with Meubel Matang.
     */
    public function test_multiple_products_cart_matang_checkout(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 2); // 2 x 1.760.000 (diff: 2 x 20.000 = 40.000)
        $cartService->add($this->productMeja->id, 1); // 1 x 3.500.000 (diff: 1 x 100.000 = 100.000)

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('meubel_type', 'matang')
            ->set('customer_name', 'Budi Santoso')
            ->set('customer_phone', '08123456789')
            ->set('province', 'Jawa Tengah')
            ->set('city', 'Jepara')
            ->set('shipping_address', 'Jl. Pemuda No 10')
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertRedirect();

        $order = Order::latest()->first();
        $this->assertEquals('matang', $order->meubel_type);
        // Subtotal: 3.520.000 + 3.500.000 = 7.020.000
        // Matang Fee: 40.000 + 100.000 = 140.000
        // Total: 7.160.000
        $this->assertEquals(7160000, $order->total_price);
        $this->assertEquals(140000, $order->customization_fee);
    }
}
