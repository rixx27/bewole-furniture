<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Livewire\Admin\Review\ReviewDetail;
use App\Livewire\Admin\Review\ReviewTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    $this->actingAs($this->admin);

    $this->customer = User::factory()->create(['name' => 'John Customer']);

    $this->category = Category::firstOrCreate([
        'name' => 'Kursi',
    ], [
        'slug' => 'kursi',
    ]);

    $this->product = Product::create([
        'name' => 'Kursi Minimalis',
        'category_id' => $this->category->id,
        'slug' => 'kursi-minimalis',
        'price' => 350000,
        'stock' => 5,
        'description' => 'Kursi kayu minimalis',
        'is_active' => true,
    ]);

    $this->order = Order::create([
        'product_id' => $this->product->id,
        'user_id' => $this->customer->id,
        'order_code' => 'ORD-987654',
        'customer_name' => 'John Customer',
        'customer_phone' => '081234567890',
        'shipping_address' => 'Jl. Pemuda No. 10',
        'city' => 'Jepara',
        'quantity' => 1,
        'total_price' => 350000,
        'status' => 'completed',
        'payment_status' => 'paid',
    ]);

    $this->review = ProductReview::create([
        'user_id' => $this->customer->id,
        'product_id' => $this->product->id,
        'order_id' => $this->order->id,
        'rating' => 5,
        'comment' => 'Kualitas sangat memuaskan dan finishing rapi.',
        'is_visible' => true,
        'is_verified' => true,
    ]);
});

test('admin can view review table and open review detail modal', function () {
    Livewire::test(ReviewTable::class)
        ->assertSee('Kursi Minimalis')
        ->assertSee('John Customer')
        ->call('openDetail', $this->review->id)
        ->assertSet('selectedReviewId', $this->review->id)
        ->assertSet('showDetailModal', true)
        ->assertSeeLivewire(ReviewDetail::class);
});

test('review detail component displays complete review information when mounted with reviewId', function () {
    Livewire::test(ReviewDetail::class, ['reviewId' => $this->review->id])
        ->assertSet('show', true)
        ->assertSee('Detail Ulasan')
        ->assertSee('Kursi Minimalis')
        ->assertSee('John Customer')
        ->assertSee('Kualitas sangat memuaskan dan finishing rapi.')
        ->assertSee('5/5')
        ->assertSee('Ditampilkan')
        ->assertSee('Terverifikasi');
});

test('review detail component closes when closeModal is triggered', function () {
    Livewire::test(ReviewDetail::class, ['reviewId' => $this->review->id])
        ->assertSet('show', true)
        ->dispatch('closeModal')
        ->assertSet('show', false)
        ->assertSet('review', null);
});
