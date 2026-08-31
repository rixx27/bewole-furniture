<?php

use App\Enums\OrderStatus;
use App\Livewire\Frontend\ProductDetail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::create([
        'name' => 'Meja & Kursi',
        'slug' => 'meja-kursi',
        'code' => 'MK01',
        'short_description' => 'Kategori Meja & Kursi',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $this->product = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Kursi Santai Jati',
        'slug' => 'kursi-santai-jati',
        'description' => 'Kursi santai dengan bahan kayu jati asli Jepara.',
        'short_description' => 'Kursi kayu jati premium',
        'price' => 1500000,
        'discount_percentage' => null,
        'discount_price' => 1500000,
        'material' => 'Kayu Jati Solid',
        'dimensions' => '80x75x85 cm',
        'weight' => 15,
        'thumbnail' => 'products/thumbnails/kursi.jpg',
        'stock' => 10,
        'status' => 'active',
    ]);
});

test('Test 1 — Guest cannot submit a review', function () {
    $component = Livewire::test(ProductDetail::class, ['product' => $this->product])
        ->set('rating', 5)
        ->set('comment', 'Bagus sekali!')
        ->call('submitReview');
    
    $component->assertSee('Silakan login terlebih dahulu untuk memberikan ulasan.');
    expect(ProductReview::count())->toBe(0);
});

test('Test 2 — User without completed order cannot submit review', function () {
    $user = User::factory()->create();

    // User without any order
    Livewire::actingAs($user)
        ->test(ProductDetail::class, ['product' => $this->product])
        ->set('rating', 5)
        ->set('comment', 'Sangat bagus!')
        ->call('submitReview')
        ->assertSee('Anda hanya dapat memberikan ulasan untuk produk yang sudah Anda beli dan pesanannya telah selesai.');

    expect(ProductReview::count())->toBe(0);

    // User with pending order (not completed)
    Order::create([
        'user_id' => $user->id,
        'product_id' => $this->product->id,
        'order_code' => 'ORD-1001',
        'customer_name' => $user->name,
        'customer_phone' => '081234567890',
        'customer_email' => $user->email,
        'shipping_address' => 'Jl. Mawar No. 1',
        'city' => 'Jepara',
        'meubel_type' => 'matang',
        'packing_type' => 'kardus',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::Pending->value,
    ]);

    Livewire::actingAs($user)
        ->test(ProductDetail::class, ['product' => $this->product])
        ->set('rating', 5)
        ->set('comment', 'Ulasan pending order')
        ->call('submitReview')
        ->assertSee('Anda hanya dapat memberikan ulasan untuk produk yang sudah Anda beli dan pesanannya telah selesai.');

    expect(ProductReview::count())->toBe(0);
});

test('Test 3 — User with completed order can submit review', function () {
    $user = User::factory()->create();

    $order = Order::create([
        'user_id' => $user->id,
        'product_id' => $this->product->id,
        'order_code' => 'ORD-1002',
        'customer_name' => $user->name,
        'customer_phone' => '081234567890',
        'customer_email' => $user->email,
        'shipping_address' => 'Jl. Mawar No. 2',
        'city' => 'Jepara',
        'meubel_type' => 'matang',
        'packing_type' => 'kardus',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::Completed->value,
    ]);

    Livewire::actingAs($user)
        ->test(ProductDetail::class, ['product' => $this->product])
        ->set('rating', 5)
        ->set('comment', 'Kualitas jati sangat memuaskan dan finishing halus.')
        ->call('submitReview')
        ->assertSee('Ulasan Berhasil Dikirim!')
        ->assertDispatched('notify');

    $review = ProductReview::first();
    expect($review)->not->toBeNull()
        ->and($review->user_id)->toBe($user->id)
        ->and($review->product_id)->toBe($this->product->id)
        ->and($review->order_id)->toBe($order->id)
        ->and($review->rating)->toBe(5)
        ->and($review->comment)->toBe('Kualitas jati sangat memuaskan dan finishing halus.')
        ->and($review->is_visible)->toBeFalse(); // Pending moderation
});

test('Test 4 & 5 — Review moderation and visibility display on product detail', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso']);

    $order = Order::create([
        'user_id' => $user->id,
        'product_id' => $this->product->id,
        'order_code' => 'ORD-1003',
        'customer_name' => $user->name,
        'customer_phone' => '081234567890',
        'customer_email' => $user->email,
        'shipping_address' => 'Jl. Mawar No. 3',
        'city' => 'Jepara',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::Completed->value,
    ]);

    // Create review with is_visible = false (pending)
    $review = ProductReview::create([
        'user_id' => $user->id,
        'product_id' => $this->product->id,
        'order_id' => $order->id,
        'rating' => 5,
        'comment' => 'Review ini masih menunggu moderasi.',
        'is_verified' => true,
        'is_visible' => false,
    ]);

    // Public should NOT see the pending review
    Livewire::test(ProductDetail::class, ['product' => $this->product])
        ->assertDontSee('Review ini masih menunggu moderasi.');

    // Admin approves review -> is_visible = true
    $review->update(['is_visible' => true]);

    // Public should NOW see the approved review
    Livewire::test(ProductDetail::class, ['product' => $this->product])
        ->assertSee('Budi Santoso')
        ->assertSee('Review ini masih menunggu moderasi.');

    // If admin hides review -> is_visible = false
    $review->update(['is_visible' => false]);

    Livewire::test(ProductDetail::class, ['product' => $this->product])
        ->assertDontSee('Review ini masih menunggu moderasi.');
});

test('Test 6 — Review with valid photo uploads and renders after approval', function () {
    Storage::fake('public');

    $user = User::factory()->create(['name' => 'Citra Dewi']);

    $order = Order::create([
        'user_id' => $user->id,
        'product_id' => $this->product->id,
        'order_code' => 'ORD-1004',
        'customer_name' => $user->name,
        'customer_phone' => '081234567890',
        'shipping_address' => 'Jl. Mawar No. 4',
        'city' => 'Jepara',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::Completed->value,
    ]);

    $file = UploadedFile::fake()->image('review1.jpg', 800, 600);

    Livewire::actingAs($user)
        ->test(ProductDetail::class, ['product' => $this->product])
        ->set('rating', 4)
        ->set('comment', 'Foto kursi asli di ruang tamu saya.')
        ->set('photos', [$file])
        ->call('submitReview')
        ->assertSee('Ulasan Berhasil Dikirim!');

    $review = ProductReview::with('images')->first();
    expect($review)->not->toBeNull()
        ->and($review->images->count())->toBe(1);

    $storedImagePath = $review->images->first()->image;
    Storage::disk('public')->assertExists($storedImagePath);

    // Approve review and verify photo path renders
    $review->update(['is_visible' => true]);

    Livewire::test(ProductDetail::class, ['product' => $this->product])
        ->assertSee(asset('storage/' . $storedImagePath));
});

test('Test 7 — Duplicate review is prevented', function () {
    $user = User::factory()->create();

    $order = Order::create([
        'user_id' => $user->id,
        'product_id' => $this->product->id,
        'order_code' => 'ORD-1005',
        'customer_name' => $user->name,
        'customer_phone' => '081234567890',
        'shipping_address' => 'Jl. Mawar No. 5',
        'city' => 'Jepara',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::Completed->value,
    ]);

    // First review submission
    Livewire::actingAs($user)
        ->test(ProductDetail::class, ['product' => $this->product])
        ->set('rating', 5)
        ->set('comment', 'Ulasan pertama')
        ->call('submitReview')
        ->assertSee('Ulasan Berhasil Dikirim!');

    expect(ProductReview::count())->toBe(1);

    // Second review submission attempt
    Livewire::actingAs($user)
        ->test(ProductDetail::class, ['product' => $this->product])
        ->set('rating', 4)
        ->set('comment', 'Ulasan kedua duplicate')
        ->call('submitReview')
        ->assertSee('Anda sudah memberikan ulasan untuk produk ini.');

    expect(ProductReview::count())->toBe(1);
});

test('Order show page displays review CTA button for completed order without review and displays review info when reviewed', function () {
    $user = User::factory()->create();

    $order = Order::create([
        'user_id' => $user->id,
        'product_id' => $this->product->id,
        'order_code' => 'ORD-1006',
        'customer_name' => $user->name,
        'customer_phone' => '081234567890',
        'shipping_address' => 'Jl. Mawar No. 6',
        'city' => 'Jepara',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::Completed->value,
    ]);

    // When order has no review yet, see review CTA
    $response = $this->actingAs($user)->get(route('orders.show', $order->order_code));
    $response->assertOk();
    $response->assertSee('Beri Ulasan Produk');
    $response->assertSee('Tulis Ulasan');
    $response->assertSee(route('products.show', $this->product->slug) . '#ulasan');

    // Create review for this order
    ProductReview::create([
        'user_id' => $user->id,
        'product_id' => $this->product->id,
        'order_id' => $order->id,
        'rating' => 5,
        'comment' => 'Kursi sangat nyaman dan pengiriman cepat.',
        'is_verified' => true,
        'is_visible' => false,
    ]);

    // Now order page should show user's review and pending moderation status
    $response = $this->actingAs($user)->get(route('orders.show', $order->order_code));
    $response->assertOk();
    $response->assertSee('Ulasan Anda');
    $response->assertSee('Menunggu Moderasi');
    $response->assertSee('Kursi sangat nyaman dan pengiriman cepat.');
});


