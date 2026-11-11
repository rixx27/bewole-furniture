<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderCodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductPriceInquiryTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Product $normalProduct;
    protected Product $hiddenPriceProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Meja & Kursi',
            'slug' => 'meja-kursi',
        ]);

        $this->normalProduct = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Kursi Santai Reguler',
            'slug' => 'kursi-santai-reguler',
            'price' => 1500000,
            'price_matang' => 1700000,
            'show_price' => true,
            'status' => 'active',
            'stock' => 10,
        ]);

        $this->hiddenPriceProduct = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Set Meja Makan Ukir Jepara',
            'slug' => 'set-meja-makan-ukir-jepara',
            'price' => 0,
            'price_matang' => 0,
            'show_price' => false,
            'status' => 'active',
            'stock' => 5,
        ]);
    }

    /**
     * TEST 1: CartService prevents adding products with show_price = false.
     */
    public function test_cart_service_blocks_hidden_price_product(): void
    {
        $cartService = app(CartService::class);

        // Try adding hidden price product
        $cartService->add($this->hiddenPriceProduct->id, 1);
        $this->assertEquals(0, $cartService->getItemCount());

        // Normal product should be allowed
        $cartService->add($this->normalProduct->id, 1);
        $this->assertEquals(1, $cartService->getItemCount());
    }

    /**
     * TEST: Inquiry query param auto-opens modal on product detail page.
     */
    public function test_inquiry_query_param_auto_opens_modal(): void
    {
        Livewire::withQueryParams(['inquiry' => 1])
            ->test(\App\Livewire\Frontend\ProductDetail::class, ['product' => $this->hiddenPriceProduct])
            ->assertSet('showInquiryModal', true);
    }

    /**
     * TEST: Inquiry regional cascading (province loads cities, changing province resets city).
     */
    public function test_inquiry_modal_loads_regional_provinces_and_cascades_cities(): void
    {
        $component = Livewire::test(\App\Livewire\Frontend\ProductDetail::class, ['product' => $this->hiddenPriceProduct])
            ->call('openInquiryModal')
            ->assertSet('showInquiryModal', true);

        // Verify provinces loaded
        $provinces = $component->get('inquiryProvinces');
        $this->assertNotEmpty($provinces);
        $this->assertContains('Jawa Tengah', $provinces);

        // Initially cities should be empty
        $this->assertEmpty($component->get('inquiryCities'));

        // Select province Jawa Tengah
        $component->set('inquiryProvince', 'Jawa Tengah');
        $cities = $component->get('inquiryCities');
        $this->assertNotEmpty($cities);
        $this->assertContains('Kabupaten Jepara', $cities);
        $this->assertContains('Kabupaten Kudus', $cities);

        // Select a city
        $component->set('inquiryCity', 'Kabupaten Jepara');
        $this->assertEquals('Kabupaten Jepara', $component->get('inquiryCity'));

        // Changing province should reset city and update cities list
        $component->set('inquiryProvince', 'Bali');
        $this->assertEquals('', $component->get('inquiryCity'));
        $this->assertContains('Kota Denpasar', $component->get('inquiryCities'));
    }

    /**
     * TEST: Inquiry validation requires province and city.
     */
    public function test_inquiry_modal_requires_province_and_city(): void
    {
        Livewire::test(\App\Livewire\Frontend\ProductDetail::class, ['product' => $this->hiddenPriceProduct])
            ->call('openInquiryModal')
            ->set('inquiryName', 'Ahmad Dani')
            ->set('inquiryPhone', '08123456789')
            ->set('inquiryProvince', '')
            ->set('inquiryCity', '')
            ->call('submitInquiry')
            ->assertHasErrors(['inquiryProvince', 'inquiryCity']);
    }

    /**
     * TEST 2: Product detail page shows Hubungi CS and inquiry modal works.
     */
    public function test_product_detail_inquiry_creates_standard_order(): void
    {
        Livewire::test(\App\Livewire\Frontend\ProductDetail::class, ['product' => $this->hiddenPriceProduct])
            ->call('openInquiryModal')
            ->assertSet('showInquiryModal', true)
            ->set('inquiryName', 'Ahmad Dani')
            ->set('inquiryPhone', '08123456789')
            ->set('inquiryProvince', 'Jawa Tengah')
            ->set('inquiryCity', 'Kabupaten Kudus')
            ->set('inquiryQuantity', 2)
            ->set('inquiryMeubelType', 'finished')
            ->set('inquiryNotes', 'Minta finishing walnut gloss')
            ->call('submitInquiry')
            ->assertHasNoErrors()
            ->assertSet('showInquiryModal', false)
            ->assertDispatched('open-wa-window');

        // Check order was created in DB
        $order = Order::where('customer_name', 'Ahmad Dani')->first();
        $this->assertNotNull($order);
        $this->assertStringStartsWith('BWL-', $order->order_code);
        $this->assertStringNotContainsString('BWL-CST', $order->order_code);
        $this->assertEquals($this->hiddenPriceProduct->id, $order->product_id);
        $this->assertEquals(2, $order->quantity);
        $this->assertEquals('finished', $order->meubel_type);
        $this->assertEquals('Kabupaten Kudus', $order->city);
        $this->assertStringContainsString('Provinsi: Jawa Tengah', $order->shipping_address);
        $this->assertEquals(0, $order->total_price);
        $this->assertEquals('Menunggu Kesepakatan', $order->formatted_total_price);

        // Check order items
        $this->assertCount(1, $order->items);
        $this->assertEquals($this->hiddenPriceProduct->id, $order->items->first()->product_id);
        $this->assertEquals(2, $order->items->first()->quantity);
    }

    /**
     * TEST 3: Admin can view and set price for the inquiry order.
     */
    public function test_admin_can_set_price_for_inquiry_order(): void
    {
        $admin = User::factory()->create();

        $orderCodeGenerator = app(OrderCodeGenerator::class);
        $orderCode = $orderCodeGenerator->generate();

        $order = Order::create([
            'order_code' => $orderCode,
            'is_custom' => false,
            'product_id' => $this->hiddenPriceProduct->id,
            'customer_name' => 'Siti Nurhaliza',
            'customer_phone' => '08987654321',
            'shipping_address' => 'Jl. Kartini No 5',
            'city' => 'Jepara',
            'meubel_type' => 'finished',
            'quantity' => 1,
            'product_subtotal' => 0,
            'total_price' => 0,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'manual_transfer',
        ]);

        $order->items()->create([
            'product_id' => $this->hiddenPriceProduct->id,
            'quantity' => 1,
            'meubel_type' => 'finished',
            'unit_price' => 0,
            'total_price' => 0,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\Order\OrderDetail::class)
            ->call('loadOrder', $order->id)
            ->assertSet('show', true)
            ->set('customPriceInput', '4500000')
            ->call('saveCustomPrice')
            ->assertDispatched('orderUpdated');

        $order->refresh();
        $this->assertEquals(4500000, $order->total_price);
        $this->assertEquals(4500000, $order->product_subtotal);
        $this->assertEquals('Rp 4.500.000', $order->formatted_total_price);
        $this->assertEquals(4500000, $order->items->first()->total_price);
    }

    /**
     * TEST 4: Customer can track order and see initial pending status then updated price.
     */
    public function test_customer_can_track_inquiry_order(): void
    {
        $orderCodeGenerator = app(OrderCodeGenerator::class);
        $orderCode = $orderCodeGenerator->generate();

        $order = Order::create([
            'order_code' => $orderCode,
            'is_custom' => false,
            'product_id' => $this->hiddenPriceProduct->id,
            'customer_name' => 'Bambang Pamungkas',
            'customer_phone' => '081122334455',
            'shipping_address' => 'Jl. Sudirman No 12',
            'city' => 'Jakarta',
            'meubel_type' => 'finished',
            'quantity' => 1,
            'product_subtotal' => 0,
            'total_price' => 0,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'manual_transfer',
        ]);

        Livewire::test(\App\Livewire\Frontend\OrderTracking::class, ['orderCode' => $orderCode])
            ->assertSee($orderCode)
            ->assertSee('Menunggu Kesepakatan');
    }
}

