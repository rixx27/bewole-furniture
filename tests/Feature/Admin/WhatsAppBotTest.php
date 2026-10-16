<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\WhatsAppNotificationLog;
use App\Models\WebsiteSetting;
use App\Enums\OrderStatus;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $this->admin = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $this->admin->assignRole($adminRole);

    WebsiteSetting::firstOrCreate([], [
        'site_name' => 'Bewole Furniture',
        'whatsapp_notification_enabled' => true,
    ]);
});

test('admin can access whatsapp bot management page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.whatsapp.index'))
        ->assertOk()
        ->assertSee('Integrasi WhatsApp Bot');
});

test('admin can toggle bot notifications via livewire', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Admin\Whatsapp\WhatsappManager::class)
        ->assertSet('botEnabled', true)
        ->call('toggleBot')
        ->assertSet('botEnabled', false);

    expect(\App\Models\WebsiteSetting::first()->whatsapp_notification_enabled)->toBeFalse();
});

test('order detail page shows whatsapp notification card', function () {
    $category = Category::firstOrCreate(['name' => 'Kursi'], ['slug' => 'kursi']);
    $product = Product::firstOrCreate(['name' => 'Kursi Santai'], [
        'category_id' => $category->id,
        'slug' => 'kursi-santai',
        'price' => 500000,
        'stock' => 10,
        'description' => 'Kursi kayu jati',
        'is_active' => true,
    ]);

    $order = Order::create([
        'product_id' => $product->id,
        'order_code' => 'BWL-TEST-' . rand(100, 999),
        'customer_name' => 'John Doe',
        'customer_phone' => '08123456789',
        'shipping_address' => 'Jl. Test No 123',
        'city' => 'Jepara',
        'quantity' => 1,
        'total_price' => 500000,
        'status' => OrderStatus::Pending->value,
        'payment_status' => 'unpaid',
    ]);

    WhatsAppNotificationLog::create([
        'order_id' => $order->id,
        'order_code' => $order->order_code,
        'recipient_phone' => '628882686430',
        'status_trigger' => 'ready_to_ship',
        'message' => 'Halo ini pesan tes pesanan',
        'status' => 'sent',
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.orders.show', $order))
        ->assertOk()
        ->assertSee('Chat Notifikasi WA')
        ->assertSee('Halo ini pesan tes pesanan');
});
