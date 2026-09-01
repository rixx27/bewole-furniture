<?php

use App\Livewire\Frontend\CartPage;
use App\Livewire\Frontend\FeaturedProducts;
use App\Livewire\Frontend\NavbarCart;
use App\Livewire\Frontend\ProductCatalog;
use App\Livewire\Frontend\ProductDetail;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::create([
        'name' => 'Kursi Kayu',
        'slug' => 'kursi-kayu',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $this->product = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Kursi Minimalis Jati',
        'slug' => 'kursi-minimalis-jati',
        'description' => 'Deskripsi kursi minimalis jati yang mewah',
        'price' => 1500000,
        'discount_price' => 1350000,
        'stock' => 10,
        'status' => 'active',
        'is_featured' => true,
    ]);
});

test('user adding product from catalog updates cart realtime with exact product data', function () {
    Livewire::test(ProductCatalog::class)
        ->call('addToCart', $this->product->id)
        ->assertDispatched('cart-updated', function ($event, $params) {
            return ($params['count'] ?? null) === 1
                && ($params['product']['id'] ?? null) === $this->product->id
                && ($params['product']['name'] ?? null) === 'Kursi Minimalis Jati'
                && ($params['product']['price'] ?? null) === 1350000;
        })
        ->assertDispatched('notify', function ($event, $params) {
            return str_contains($params['message'] ?? '', 'Kursi Minimalis Jati')
                && ($params['product_name'] ?? null) === 'Kursi Minimalis Jati';
        });

    $cart = app(CartService::class)->getCart();
    expect($cart)->toHaveKey($this->product->id);
    expect($cart[$this->product->id]['name'])->toBe('Kursi Minimalis Jati');
    expect($cart[$this->product->id]['quantity'])->toBe(1);
});

test('user adding product from product detail updates cart realtime with selected quantity and exact data', function () {
    Livewire::test(ProductDetail::class, ['product' => $this->product])
        ->set('quantity', 3)
        ->call('addToCart')
        ->assertDispatched('cart-updated', function ($event, $params) {
            return ($params['count'] ?? null) === 3
                && ($params['product']['id'] ?? null) === $this->product->id
                && ($params['product']['quantity'] ?? null) === 3;
        })
        ->assertDispatched('notify', function ($event, $params) {
            return str_contains($params['message'] ?? '', 'Kursi Minimalis Jati (3x)')
                && ($params['product_name'] ?? null) === 'Kursi Minimalis Jati';
        });

    $cart = app(CartService::class)->getCart();
    expect($cart[$this->product->id]['quantity'])->toBe(3);
});

test('user adding product multiple times increments quantity and does not reset to 1', function () {
    $cartService = app(CartService::class);
    $cartService->add($this->product->id, 1);
    expect($cartService->getItemCount())->toBe(1);

    $cartService->add($this->product->id, 1);
    expect($cartService->getItemCount())->toBe(2);

    $cart = $cartService->getCart();
    expect($cart[$this->product->id]['quantity'])->toBe(2);
});

test('user adding product from homepage featured products updates cart realtime with exact data', function () {
    Livewire::test(FeaturedProducts::class)
        ->call('addToCart', $this->product->id)
        ->assertDispatched('cart-updated', function ($event, $params) {
            return ($params['count'] ?? null) === 1
                && ($params['product']['name'] ?? null) === 'Kursi Minimalis Jati';
        })
        ->assertDispatched('notify');

    $cart = app(CartService::class)->getCart();
    expect($cart)->toHaveKey($this->product->id);
});

test('navbar cart component updates quantity realtime on cart-updated event', function () {
    $navbar = Livewire::test(NavbarCart::class);
    expect($navbar->get('cartQty'))->toBe(0);

    // Simulate cart updated event
    $navbar->dispatch('cart-updated', count: 5);
    expect($navbar->get('cartQty'))->toBe(5);
});

test('cart page component updates subtotal and cart data realtime on cart-updated event', function () {
    app(CartService::class)->add($this->product->id, 2);

    $cartPage = Livewire::test(CartPage::class);
    expect($cartPage->get('subtotal'))->toBe(2700000);

    // Update quantity via cart page action
    $cartPage->call('updateQuantity', $this->product->id, 4);
    expect($cartPage->get('subtotal'))->toBe(5400000);
});

test('product detail route /produk/{slug} returns 200 and renders detail page', function () {
    $response = $this->get(route('products.show', $this->product->slug));
    $response->assertOk();
    $response->assertSee('Kursi Minimalis Jati');
    $response->assertSee('Spesifikasi Produk');
    $response->assertSee('Tambah ke Keranjang');
});

test('catalog page contains valid links to product detail', function () {
    $response = $this->get(route('products.index'));
    $response->assertOk();
    $response->assertSee(route('products.show', $this->product->slug));
});
