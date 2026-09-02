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
            'packing_fee' => 12000,
            'status' => 'active',
            'stock' => 10,
        ]);

        $this->productMeja = Product::create([
            'category_id' => $this->categoryMeja->id,
            'name' => 'Meja Makan Jati',
            'slug' => 'meja-makan-jati',
            'price' => 3500000,
            'price_matang' => 3600000,
            'packing_fee' => 15000,
            'status' => 'active',
            'stock' => 5,
        ]);
    }

    /**
     * TEST 1: Meubel Mentah success without custom fee & seating choice hidden.
     */
    public function test_1_meubel_mentah_checkout_success(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 5);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('meubel_type', 'mentah')
            ->set('packing_type', 'kardus')
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
            'total_price' => 8800000, // 5 x 1.760.000 (biaya packing termasuk ongkir)
            'meubel_type' => 'mentah',
            'packing_type' => 'kardus',
        ]);
    }

    /**
     * TEST 2: Meubel Matang with seating option Kulit and Kardus packing.
     */
    public function test_2_meubel_matang_kulit_kardus_success(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 5);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('meubel_type', 'matang')
            ->set("customization_selections.{$this->productKursi->id}", 'Kulit')
            ->set('packing_type', 'kardus')
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
        $this->assertEquals('kardus', $order->packing_type);
        $this->assertEquals([(string)$this->productKursi->id => 'Kulit'], $order->customization_details);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->productKursi->id,
            'meubel_type' => 'matang',
            'customization_option' => 'Kulit',
            'packing_type' => 'kardus',
        ]);
    }

    /**
     * TEST 3: Meubel Matang with seating option Benang and Plastik packing.
     */
    public function test_3_meubel_matang_benang_plastik_success(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 5);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('meubel_type', 'matang')
            ->set("customization_selections.{$this->productKursi->id}", 'Benang')
            ->set('packing_type', 'plastik')
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
        $this->assertEquals('plastik', $order->packing_type);
        $this->assertEquals([(string)$this->productKursi->id => 'Benang'], $order->customization_details);
    }

    /**
     * TEST 4: Meubel Matang requires seating option for Kursi.
     */
    public function test_4_meubel_matang_without_seating_option_fails(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 2);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('meubel_type', 'matang')
            ->set('packing_type', 'kardus')
            ->set('customer_name', 'Budi Santoso')
            ->set('customer_phone', '08123456789')
            ->set('province', 'Jawa Tengah')
            ->set('city', 'Jepara')
            ->set('shipping_address', 'Jl. Pemuda No 10')
            ->call('placeOrder')
            ->assertHasErrors(["customization_selections.{$this->productKursi->id}"]);
    }

    /**
     * TEST 5: Missing meubel_type fails validation.
     */
    public function test_5_missing_meubel_type_fails(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 1);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('packing_type', 'kardus')
            ->set('customer_name', 'Budi Santoso')
            ->set('customer_phone', '08123456789')
            ->set('province', 'Jawa Tengah')
            ->set('city', 'Jepara')
            ->set('shipping_address', 'Jl. Pemuda No 10')
            ->call('placeOrder')
            ->assertHasErrors(['meubel_type']);
    }

    /**
     * TEST 6: Missing packing_type fails validation.
     */
    public function test_6_missing_packing_type_fails(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 1);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('meubel_type', 'mentah')
            ->set('customer_name', 'Budi Santoso')
            ->set('customer_phone', '08123456789')
            ->set('province', 'Jawa Tengah')
            ->set('city', 'Jepara')
            ->set('shipping_address', 'Jl. Pemuda No 10')
            ->call('placeOrder')
            ->assertHasErrors(['packing_type']);
    }

    /**
     * TEST 7: Switching from Matang (with selection) back to Mentah resets selection.
     */
    public function test_7_switching_to_mentah_resets_customization(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 1);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('meubel_type', 'matang')
            ->set("customization_selections.{$this->productKursi->id}", 'Kulit')
            ->set('meubel_type', 'mentah')
            ->assertSet('customization_selections', []);
    }

    /**
     * TEST 8: Cart with Kursi + Meja (Kursi requires seating, Meja does not).
     */
    public function test_8_multiple_products_cart_customization(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->productKursi->id, 2);
        $cartService->add($this->productMeja->id, 1);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Frontend\CheckoutPage::class)
            ->set('meubel_type', 'matang')
            ->set("customization_selections.{$this->productKursi->id}", 'Anyaman')
            ->set('packing_type', 'plastik')
            ->set('customer_name', 'Budi Santoso')
            ->set('customer_phone', '08123456789')
            ->set('province', 'Jawa Tengah')
            ->set('city', 'Jepara')
            ->set('shipping_address', 'Jl. Pemuda No 10')
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertRedirect();

        $order = Order::latest()->first();
        $this->assertEquals([(string)$this->productKursi->id => 'Anyaman'], $order->customization_details);
    }
}
