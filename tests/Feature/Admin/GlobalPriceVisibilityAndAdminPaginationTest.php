<?php

use App\Helpers\Currency;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Storage::fake('public');
    Cache::flush();

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->category = Category::create([
        'name' => 'Meja & Kursi',
        'slug' => 'meja-kursi',
        'code' => 'MJK',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $this->productA = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Meja Jati A',
        'slug' => 'meja-jati-a',
        'description' => 'Deskripsi Meja Jati A',
        'short_description' => 'Meja jati',
        'price' => 2500000,
        'price_usd' => 150,
        'material' => 'Kayu Jati',
        'show_price' => true,
        'status' => 'active',
        'stock' => 5,
    ]);

    $this->productB = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Kursi Jati B',
        'slug' => 'kursi-jati-b',
        'description' => 'Deskripsi Kursi Jati B',
        'short_description' => 'Kursi jati',
        'price' => 1200000,
        'price_usd' => 75,
        'material' => 'Kayu Jati',
        'show_price' => true,
        'status' => 'active',
        'stock' => 10,
    ]);
});

test('disabling show_price on one product disables all products and hides currency converter, and vice versa', function () {
    $this->actingAs($this->admin);

    // Initially converter is visible on frontend
    expect(Currency::hasVisibleProductPrices())->toBeTrue();
    $homeBefore = $this->get(route('home'));
    $homeBefore->assertOk();
    $homeBefore->assertSee('aria-label="Pilih mata uang"', false);
    $homeBefore->assertSee('Pilih Mata Uang');

    // Disable show_price on productA via admin update
    $response = $this->put(route('admin.products.update', $this->productA), [
        'category_id' => $this->category->id,
        'name' => $this->productA->name,
        'slug' => $this->productA->slug,
        'description' => $this->productA->description,
        'short_description' => $this->productA->short_description,
        'price' => '2.500.000',
        'material' => 'Kayu Jati',
        'stock' => 5,
        'status' => 'active',
        'show_price' => '0',
    ]);

    $response->assertRedirect(route('admin.products.index'));

    // Both products must now have show_price = false
    expect($this->productA->fresh()->show_price)->toBeFalse();
    expect($this->productB->fresh()->show_price)->toBeFalse();
    expect(Currency::hasVisibleProductPrices())->toBeFalse();

    // Currency converter must now be hidden from frontend navbar (both desktop & mobile)
    $homeDisabled = $this->get(route('home'));
    $homeDisabled->assertOk();
    $homeDisabled->assertDontSee('aria-label="Pilih mata uang"', false);
    $homeDisabled->assertDontSee('Pilih Mata Uang');

    // Re-enable show_price on productB via admin update
    $reEnableResponse = $this->put(route('admin.products.update', $this->productB), [
        'category_id' => $this->category->id,
        'name' => $this->productB->name,
        'slug' => $this->productB->slug,
        'description' => $this->productB->description,
        'short_description' => $this->productB->short_description,
        'price' => '1.200.000',
        'material' => 'Kayu Jati',
        'stock' => 10,
        'status' => 'active',
        'show_price' => '1',
    ]);

    $reEnableResponse->assertRedirect(route('admin.products.index'));

    // Both products must now have show_price = true and converter returns
    expect($this->productA->fresh()->show_price)->toBeTrue();
    expect($this->productB->fresh()->show_price)->toBeTrue();
    expect(Currency::hasVisibleProductPrices())->toBeTrue();

    $homeReEnabled = $this->get(route('home'));
    $homeReEnabled->assertOk();
    $homeReEnabled->assertSee('aria-label="Pilih mata uang"', false);
    $homeReEnabled->assertSee('Pilih Mata Uang');
});

test('admin quick toggle global price syncs all products and create form inherits current global state', function () {
    $this->actingAs($this->admin);

    // Quick toggle OFF
    $this->patch(route('admin.products.toggle-global-price'), ['show_price' => '0'])
        ->assertRedirect();

    expect(Product::where('show_price', true)->count())->toBe(0);
    expect(Currency::hasVisibleProductPrices())->toBeFalse();

    // Create page defaults toggle to false when global prices are disabled
    $createPage = $this->get(route('admin.products.create'));
    $createPage->assertOk();
    $createPage->assertSee('checked: false', false);

    // Quick toggle ON
    $this->patch(route('admin.products.toggle-global-price'), ['show_price' => '1'])
        ->assertRedirect();

    expect(Product::where('show_price', false)->count())->toBe(0);
    expect(Currency::hasVisibleProductPrices())->toBeTrue();

    // Create page defaults toggle to true when global prices are enabled
    $createPageOn = $this->get(route('admin.products.create'));
    $createPageOn->assertOk();
    $createPageOn->assertSee('checked: true', false);
});

test('admin pagination renders floating pill design in both blade controller and livewire views', function () {
    $this->actingAs($this->admin);

    // Create enough products to trigger pagination (>10)
    for ($i = 1; $i <= 12; $i++) {
        Product::create([
            'category_id' => $this->category->id,
            'name' => "Produk Ekstra {$i}",
            'slug' => "produk-ekstra-{$i}",
            'description' => 'Deskripsi',
            'price' => 1000000,
            'show_price' => true,
            'status' => 'active',
            'stock' => 5,
        ]);
    }

    $indexResponse = $this->get(route('admin.products.index'));
    $indexResponse->assertOk();
    $indexResponse->assertSee('bg-wood-primary-dark/95', false);
    $indexResponse->assertSee('Previous');
    $indexResponse->assertSee('Next');
    $indexResponse->assertSee('?page=2', false);

    // Also verify Livewire mode renders wire:click buttons
    $items = collect(range(1, 25));
    $paginator = new \Illuminate\Pagination\LengthAwarePaginator($items->slice(0, 10), 25, 10, 1);
    $livewireHtml = $paginator->links('components.pagination.admin-floating-pill', ['isLivewire' => true])->toHtml();

    expect($livewireHtml)->toContain('bg-wood-primary-dark/95');
    expect($livewireHtml)->toContain('wire:click="gotoPage(2,');
    expect($livewireHtml)->toContain('wire:click="nextPage(');
});
