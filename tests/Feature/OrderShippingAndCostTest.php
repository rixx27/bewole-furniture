<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingMethod;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function createShippingCostTestOrder(array $attributes = []): Order {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Meja Kursi',
        'slug' => 'meja-kursi-' . uniqid(),
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Meja Makan Jati',
        'slug' => 'meja-makan-jati-' . uniqid(),
        'price' => 2000000,
        'status' => 'active',
        'stock' => 5,
    ]);

    return Order::create(array_merge([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'order_code' => 'ORD-' . strtoupper(uniqid()),
        'customer_name' => 'Ahmad Pelanggan',
        'customer_phone' => '081298765432',
        'shipping_address' => 'Jl. Kartini No 45, RT 02/03',
        'city' => 'Semarang',
        'postal_code' => '50123',
        'quantity' => 1,
        'product_subtotal' => 2000000,
        'total_price' => 2000000,
        'status' => OrderStatus::Pending->value,
        'payment_status' => PaymentStatus::Unpaid->value,
        'down_payment_amount' => 0,
    ], $attributes));
}

test('order model supports shipping cost and payment scheme attributes', function () {
    $order = createShippingCostTestOrder([
        'total_price' => 1500000,
        'product_subtotal' => 1500000,
        'shipping_cost' => 350000,
        'shipping_cost_type' => 'buyer_to_driver',
        'shipping_method' => ShippingMethod::Expedition->value,
        'courier' => 'Aji Jaya Cargo',
        'tracking_number' => 'AJ-12345',
    ]);

    expect((float) $order->shipping_cost)->toBe(350000.0);
    expect($order->formatted_shipping_cost)->toBe('Rp 350.000');
    expect($order->shipping_cost_type_label)->toBe('Bayar di Tempat ke Sopir (COD Ongkir)');
    expect($order->is_shipping_configured)->toBeTrue();
    expect($order->subtotal)->toBe(1500000.0);
});

test('order service updateShipping calculates total price correctly when billed to order', function () {
    $order = createShippingCostTestOrder([
        'total_price' => 2000000,
        'product_subtotal' => 2000000,
        'status' => OrderStatus::ReadyToShip->value,
        'payment_status' => PaymentStatus::Unpaid->value,
    ]);

    $orderService = app(OrderService::class);
    $updatedOrder = $orderService->updateShipping($order, [
        'shipping_method' => ShippingMethod::Expedition->value,
        'courier' => 'Kargo Jepara Indah',
        'tracking_number' => 'KJP-998',
        'shipping_date' => now()->format('Y-m-d'),
        'shipping_cost' => 400000,
        'shipping_cost_type' => 'billed_to_order',
    ]);

    expect((float) $updatedOrder->product_subtotal)->toBe(2000000.0);
    expect((float) $updatedOrder->shipping_cost)->toBe(400000.0);
    expect((float) $updatedOrder->total_price)->toBe(2400000.0);
    expect($updatedOrder->status)->toBe(OrderStatus::Shipped->value);
});

test('order service updateShipping keeps total price unchanged when buyer pays driver', function () {
    $order = createShippingCostTestOrder([
        'total_price' => 2000000,
        'product_subtotal' => 2000000,
        'status' => OrderStatus::InProduction->value,
    ]);

    $orderService = app(OrderService::class);
    $updatedOrder = $orderService->updateShipping($order, [
        'shipping_method' => ShippingMethod::InternalDelivery->value,
        'driver_name' => 'Pak Budi',
        'vehicle_number' => 'K 1234 XY',
        'shipping_date' => now()->format('Y-m-d'),
        'shipping_cost' => 250000,
        'shipping_cost_type' => 'buyer_to_driver',
    ]);

    expect((float) $updatedOrder->product_subtotal)->toBe(2000000.0);
    expect((float) $updatedOrder->shipping_cost)->toBe(250000.0);
    expect((float) $updatedOrder->total_price)->toBe(2000000.0);
    expect($updatedOrder->shipping_cost_type)->toBe('buyer_to_driver');
});

test('admin can save shipping cost through Livewire OrderShipping component', function () {
    $admin = User::factory()->create();
    $order = createShippingCostTestOrder([
        'total_price' => 1000000,
        'product_subtotal' => 1000000,
    ]);

    Livewire::actingAs($admin)
        ->test(\App\Livewire\Admin\Order\OrderShipping::class)
        ->dispatch('openShipping', orderId: $order->id)
        ->set('shipping_method', 'expedition')
        ->set('courier', 'Hiba Trucking')
        ->set('tracking_number', 'HIB-777')
        ->set('shipping_date', now()->format('Y-m-d'))
        ->set('shipping_cost', '300.000')
        ->set('shipping_cost_type', 'buyer_to_driver')
        ->call('saveShipping')
        ->assertDispatched('orderUpdated')
        ->assertDispatched('notify');

    $order->refresh();
    expect($order->courier)->toBe('Hiba Trucking');
    expect($order->tracking_number)->toBe('HIB-777');
    expect((float) $order->shipping_cost)->toBe(300000.0);
    expect($order->shipping_cost_type)->toBe('buyer_to_driver');
});
