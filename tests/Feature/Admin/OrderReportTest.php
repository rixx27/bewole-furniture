<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Livewire\Admin\Report\OrderReport;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $this->admin = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $this->admin->assignRole('admin');

    $this->category = Category::create([
        'name' => 'Living Room',
        'slug' => 'living-room',
        'description' => 'Living room furniture',
    ]);

    $this->product = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Meja Tamu Jati Minimalis',
        'slug' => 'meja-tamu-jati-minimalis',
        'description' => 'Meja tamu elegan kayu jati',
        'price' => 1500000,
        'stock' => 10,
        'is_active' => true,
    ]);
});

test('guest is redirected to login when accessing admin reports', function () {
    $response = $this->get(route('admin.reports.orders'));
    $response->assertRedirect(route('login'));
});

test('regular user cannot access admin reports', function () {
    $regularUser = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($regularUser)
        ->get(route('admin.reports.orders'))
        ->assertForbidden();
});

test('admin can view the reports page with header and filters', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.orders'))
        ->assertOk()
        ->assertSee('Laporan')
        ->assertSee('Kelola dan export data transaksi Bewole.')
        ->assertSee('Export Excel')
        ->assertSee('Total Pesanan')
        ->assertSee('Total Pendapatan')
        ->assertSee('Pesanan Selesai')
        ->assertSee('Pesanan Dibatalkan');
});

test('livewire report component renders orders and calculates summary stats', function () {
    // Create 3 orders
    $order1 = Order::create([
        'order_code' => 'BWL-260815-0001',
        'product_id' => $this->product->id,
        'customer_name' => 'Krisna Faridiwa',
        'customer_phone' => '08123456789',
        'whatsapp_number' => '08123456789',
        'shipping_address' => 'Jl. Tahunan No. 12',
        'city' => 'Jepara',
        'postal_code' => '59427',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::Completed->value,
        'payment_status' => PaymentStatus::Paid->value,
        'created_at' => '2026-08-10 10:00:00',
    ]);

    $order2 = Order::create([
        'order_code' => 'BWL-260815-0002',
        'product_id' => $this->product->id,
        'customer_name' => 'Budi Santoso',
        'customer_phone' => '082233445566',
        'whatsapp_number' => '082233445566',
        'shipping_address' => 'Jl. Pemuda No. 45',
        'city' => 'Semarang',
        'postal_code' => '50132',
        'quantity' => 2,
        'total_price' => 3000000,
        'status' => OrderStatus::InProduction->value,
        'payment_status' => PaymentStatus::Paid->value,
        'created_at' => '2026-08-12 14:00:00',
    ]);

    $order3 = Order::create([
        'order_code' => 'BWL-260815-0003',
        'product_id' => $this->product->id,
        'customer_name' => 'Ahmad Dahlan',
        'customer_phone' => '089988776655',
        'whatsapp_number' => '089988776655',
        'shipping_address' => 'Jl. Malioboro No. 1',
        'city' => 'Yogyakarta',
        'postal_code' => '55271',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::Cancelled->value,
        'payment_status' => PaymentStatus::Unpaid->value,
        'created_at' => '2026-08-14 09:00:00',
    ]);

    Livewire::actingAs($this->admin)
        ->test(OrderReport::class)
        ->assertSee('BWL-260815-0001')
        ->assertSee('BWL-260815-0002')
        ->assertSee('BWL-260815-0003')
        ->assertSee('Krisna Faridiwa')
        ->assertSee('Budi Santoso')
        ->assertSee('Ahmad Dahlan')
        ->assertViewHas('totalOrders', 3)
        ->assertViewHas('totalRevenue', 4500000.0) // order1 (1.5M) + order2 (3.0M), order3 cancelled excluded
        ->assertViewHas('completedOrders', 1)
        ->assertViewHas('cancelledOrders', 1);
});

test('filtering by date range updates table and summary', function () {
    $order1 = Order::create([
        'order_code' => 'BWL-260801-0001',
        'product_id' => $this->product->id,
        'customer_name' => 'User Agustus Awal',
        'customer_phone' => '0811111111',
        'whatsapp_number' => '0811111111',
        'shipping_address' => 'Jl. Tahunan No. 1',
        'city' => 'Jepara',
        'postal_code' => '59427',
        'quantity' => 1,
        'total_price' => 1000000,
        'status' => OrderStatus::Completed->value,
        'payment_status' => PaymentStatus::Paid->value,
    ]);
    $order1->created_at = \Carbon\Carbon::parse('2026-08-01 10:00:00');
    $order1->updated_at = \Carbon\Carbon::parse('2026-08-01 10:00:00');
    $order1->saveQuietly();

    $order2 = Order::create([
        'order_code' => 'BWL-260815-0002',
        'product_id' => $this->product->id,
        'customer_name' => 'User Agustus Tengah',
        'customer_phone' => '0822222222',
        'whatsapp_number' => '0822222222',
        'shipping_address' => 'Jl. Tahunan No. 2',
        'city' => 'Jepara',
        'postal_code' => '59427',
        'quantity' => 1,
        'total_price' => 2000000,
        'status' => OrderStatus::Completed->value,
        'payment_status' => PaymentStatus::Paid->value,
    ]);
    $order2->created_at = \Carbon\Carbon::parse('2026-08-15 10:00:00');
    $order2->updated_at = \Carbon\Carbon::parse('2026-08-15 10:00:00');
    $order2->saveQuietly();

    Livewire::actingAs($this->admin)
        ->test(OrderReport::class)
        ->set('startDate', '2026-08-10')
        ->set('endDate', '2026-08-20')
        ->assertSee('BWL-260815-0002')
        ->assertDontSee('BWL-260801-0001')
        ->assertViewHas('totalOrders', 1)
        ->assertViewHas('totalRevenue', 2000000.0);
});

test('filtering by status updates table and summary', function () {
    Order::create([
        'order_code' => 'BWL-260810-0001',
        'product_id' => $this->product->id,
        'customer_name' => 'Pesanan Selesai Customer',
        'customer_phone' => '0811111111',
        'whatsapp_number' => '0811111111',
        'shipping_address' => 'Jl. Tahunan No. 3',
        'city' => 'Jepara',
        'postal_code' => '59427',
        'quantity' => 1,
        'total_price' => 1000000,
        'status' => OrderStatus::Completed->value,
        'payment_status' => PaymentStatus::Paid->value,
        'created_at' => '2026-08-10 10:00:00',
    ]);

    Order::create([
        'order_code' => 'BWL-260810-0002',
        'product_id' => $this->product->id,
        'customer_name' => 'Pesanan Dibatalkan Customer',
        'customer_phone' => '0822222222',
        'whatsapp_number' => '0822222222',
        'shipping_address' => 'Jl. Tahunan No. 4',
        'city' => 'Jepara',
        'postal_code' => '59427',
        'quantity' => 1,
        'total_price' => 1000000,
        'status' => OrderStatus::Cancelled->value,
        'payment_status' => PaymentStatus::Unpaid->value,
        'created_at' => '2026-08-10 11:00:00',
    ]);

    Livewire::actingAs($this->admin)
        ->test(OrderReport::class)
        ->set('status', OrderStatus::Completed->value)
        ->assertSee('BWL-260810-0001')
        ->assertDontSee('BWL-260810-0002')
        ->assertViewHas('totalOrders', 1)
        ->assertViewHas('completedOrders', 1)
        ->assertViewHas('cancelledOrders', 0);
});

test('search filters by order code, customer name, or whatsapp', function () {
    Order::create([
        'order_code' => 'BWL-260818-9999',
        'product_id' => $this->product->id,
        'customer_name' => 'Dewi Sartika',
        'customer_phone' => '081299998888',
        'whatsapp_number' => '081299998888',
        'shipping_address' => 'Jl. Kartini No. 99',
        'city' => 'Jepara',
        'postal_code' => '59411',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::Completed->value,
        'payment_status' => PaymentStatus::Paid->value,
        'created_at' => '2026-08-18 10:00:00',
    ]);

    Livewire::actingAs($this->admin)
        ->test(OrderReport::class)
        ->set('search', '9999')
        ->assertSee('Dewi Sartika')
        ->assertSee('BWL-260818-9999')
        ->set('search', 'Dewi')
        ->assertSee('BWL-260818-9999')
        ->set('search', '081299998888')
        ->assertSee('BWL-260818-9999')
        ->set('search', 'NonExistentXYZ')
        ->assertDontSee('BWL-260818-9999');
});

test('reset filters restores default view', function () {
    Livewire::actingAs($this->admin)
        ->test(OrderReport::class)
        ->set('startDate', '2026-08-01')
        ->set('endDate', '2026-08-15')
        ->set('status', OrderStatus::Completed->value)
        ->set('search', 'BWL')
        ->call('resetFilters')
        ->assertSet('startDate', '')
        ->assertSet('endDate', '')
        ->assertSet('status', '')
        ->assertSet('search', '');
});

test('combination of date, status, and search filters accurately', function () {
    $orderMatch = Order::create([
        'order_code' => 'BWL-260812-7777',
        'product_id' => $this->product->id,
        'customer_name' => 'Kombinasi User Cocok',
        'customer_phone' => '087777777777',
        'whatsapp_number' => '087777777777',
        'shipping_address' => 'Jl. Pemuda No. 77',
        'city' => 'Jepara',
        'postal_code' => '59411',
        'quantity' => 2,
        'total_price' => 3000000,
        'status' => OrderStatus::Completed->value,
        'payment_status' => PaymentStatus::Paid->value,
    ]);
    $orderMatch->created_at = \Carbon\Carbon::parse('2026-08-12 10:00:00');
    $orderMatch->saveQuietly();

    $orderMismatchStatus = Order::create([
        'order_code' => 'BWL-260812-8888',
        'product_id' => $this->product->id,
        'customer_name' => 'Kombinasi User Beda Status',
        'customer_phone' => '088888888888',
        'whatsapp_number' => '088888888888',
        'shipping_address' => 'Jl. Pemuda No. 88',
        'city' => 'Jepara',
        'postal_code' => '59411',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::InProduction->value,
        'payment_status' => PaymentStatus::Paid->value,
    ]);
    $orderMismatchStatus->created_at = \Carbon\Carbon::parse('2026-08-12 10:00:00');
    $orderMismatchStatus->saveQuietly();

    Livewire::actingAs($this->admin)
        ->test(OrderReport::class)
        ->set('startDate', '2026-08-10')
        ->set('endDate', '2026-08-15')
        ->set('status', OrderStatus::Completed->value)
        ->set('search', '7777')
        ->assertSee('BWL-260812-7777')
        ->assertDontSee('BWL-260812-8888')
        ->assertViewHas('totalOrders', 1)
        ->assertViewHas('totalRevenue', 3000000.0);
});

test('invalid date range triggers validation error', function () {
    Livewire::actingAs($this->admin)
        ->test(OrderReport::class)
        ->set('startDate', '2026-08-20')
        ->set('endDate', '2026-08-10')
        ->assertHasErrors(['dateRange']);
});

test('order report export class produces valid excel file', function () {
    $order = Order::create([
        'order_code' => 'BWL-260815-0099',
        'product_id' => $this->product->id,
        'customer_name' => 'Testing Excel Customer',
        'customer_phone' => '081234567890',
        'whatsapp_number' => '081234567890',
        'shipping_address' => 'Jl. Tahunan No. 99',
        'city' => 'Jepara',
        'postal_code' => '59427',
        'quantity' => 1,
        'total_price' => 1500000,
        'status' => OrderStatus::Completed->value,
        'payment_status' => PaymentStatus::Paid->value,
    ]);

    $export = new \App\Exports\OrderReportExport(['search' => '0099']);
    $query = $export->query();
    expect($query->count())->toBe(1);

    $mapped = $export->map($query->first());
    expect($mapped[1])->toBe('BWL-260815-0099')
        ->and($mapped[3])->toBe('Testing Excel Customer')
        ->and($mapped[9])->toBe(1500000.0)
        ->and($mapped[10])->toBe('Pesanan Selesai')
        ->and($mapped[11])->toBe('Lunas');
});
