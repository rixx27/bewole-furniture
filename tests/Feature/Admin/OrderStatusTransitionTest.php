<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Admin\Order\OrderStatusManager;

uses(RefreshDatabase::class);

beforeEach(function () {
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    $this->actingAs($this->admin);
});

function createTestOrder(string $status = 'pending'): Order
{
    $category = \App\Models\Category::firstOrCreate([
        'name' => 'Meja',
    ], [
        'slug' => 'meja',
    ]);

    $product = \App\Models\Product::firstOrCreate([
        'name' => 'Meja Jati',
    ], [
        'category_id' => $category->id,
        'slug' => 'meja-jati',
        'price' => 500000,
        'stock' => 10,
        'description' => 'Meja kayu jati berkualitas',
        'is_active' => true,
    ]);

    return Order::create([
        'product_id' => $product->id,
        'order_code' => 'BWL-08-26-' . rand(100, 999),
        'customer_name' => 'John Doe',
        'customer_phone' => '08123456789',
        'shipping_address' => 'Jl. Test No 123',
        'city' => 'Jepara',
        'quantity' => 1,
        'total_price' => 500000,
        'status' => $status,
        'payment_status' => 'unpaid',
    ]);
}

test('order status can advance one step forward', function () {
    $order = createTestOrder(OrderStatus::Pending->value);

    /** @var OrderService $orderService */
    $orderService = app(OrderService::class);

    $updatedOrder = $orderService->updateStatus($order, OrderStatus::Confirmed, 'Konfirmasi admin');

    expect($updatedOrder->status)->toBe(OrderStatus::Confirmed->value);
    expect($updatedOrder->statusHistories()->first()->description)->toBe('Konfirmasi admin');
});

test('order status cannot skip steps forward', function () {
    $order = createTestOrder(OrderStatus::Pending->value);

    /** @var OrderService $orderService */
    $orderService = app(OrderService::class);

    expect(fn() => $orderService->updateStatus($order, OrderStatus::AwaitingPayment))
        ->toThrow(\InvalidArgumentException::class, 'Status pesanan hanya dapat dilanjutkan ke tahap berikutnya atau dibatalkan.');
});

test('order status cannot move backward', function () {
    $order = createTestOrder(OrderStatus::Confirmed->value);

    /** @var OrderService $orderService */
    $orderService = app(OrderService::class);

    expect(fn() => $orderService->updateStatus($order, OrderStatus::Pending))
        ->toThrow(\InvalidArgumentException::class, 'Status pesanan hanya dapat dilanjutkan ke tahap berikutnya atau dibatalkan.');
});

test('active order status can transition to cancelled', function () {
    $order = createTestOrder(OrderStatus::InProduction->value);

    /** @var OrderService $orderService */
    $orderService = app(OrderService::class);

    $updatedOrder = $orderService->updateStatus($order, OrderStatus::Cancelled, 'Pembatalan barang');

    expect($updatedOrder->status)->toBe(OrderStatus::Cancelled->value);
});

test('completed or cancelled order status cannot be changed', function () {
    $completedOrder = createTestOrder(OrderStatus::Completed->value);
    $cancelledOrder = createTestOrder(OrderStatus::Cancelled->value);

    /** @var OrderService $orderService */
    $orderService = app(OrderService::class);

    expect(fn() => $orderService->updateStatus($completedOrder, OrderStatus::Cancelled))
        ->toThrow(\InvalidArgumentException::class);

    expect(fn() => $orderService->updateStatus($cancelledOrder, OrderStatus::Pending))
        ->toThrow(\InvalidArgumentException::class);
});

test('order status manager livewire component respects transition rules', function () {
    $order = createTestOrder(OrderStatus::Pending->value);

    Livewire::test(OrderStatusManager::class)
        ->call('loadOrder', $order->id)
        ->assertSet('newStatus', OrderStatus::Confirmed->value)
        ->set('newStatus', OrderStatus::Confirmed->value)
        ->set('notes', 'Catatan test')
        ->call('updateStatus')
        ->assertHasNoErrors();

    expect($order->fresh()->status)->toBe(OrderStatus::Confirmed->value);
});
