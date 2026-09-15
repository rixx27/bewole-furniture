<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Storage::fake('public');
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
});

test('admin can see sort_order input field in product create and edit views', function () {
    $this->actingAs($this->admin);

    $createResponse = $this->get(route('admin.products.create'));
    $createResponse->assertOk();
    $createResponse->assertSee('Urutan');
    $createResponse->assertSee('name="sort_order"', false);

    $product = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Kursi Santai',
        'slug' => 'kursi-santai',
        'description' => 'Deskripsi lengkap',
        'short_description' => 'Deskripsi singkat',
        'price' => 500000,
        'material' => 'Kayu Jati',
        'stock' => 10,
        'status' => 'active',
        'sort_order' => 5,
    ]);

    $editResponse = $this->get(route('admin.products.edit', $product));
    $editResponse->assertOk();
    $editResponse->assertSee('Urutan');
    $editResponse->assertSee('name="sort_order"', false);
    $editResponse->assertSee('value="5"', false);
});

test('admin can store a product with sort_order and it defaults properly', function () {
    $this->actingAs($this->admin);

    $response = $this->post(route('admin.products.store'), [
        'category_id' => $this->category->id,
        'name' => 'Meja Makan Minimalis',
        'slug' => 'meja-makan-minimalis',
        'short_description' => 'Meja makan jati',
        'description' => 'Meja makan jati minimalis kualitas terbaik',
        'price' => '2.500.000',
        'stock' => 5,
        'material' => 'Kayu Jati',
        'status' => 'active',
        'sort_order' => 3,
        'thumbnail' => UploadedFile::fake()->image('thumb.jpg'),
    ]);

    $response->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseHas('products', [
        'name' => 'Meja Makan Minimalis',
        'sort_order' => 3,
    ]);
});

test('admin can update a product sort_order', function () {
    $this->actingAs($this->admin);

    $product = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Kursi Bar',
        'slug' => 'kursi-bar',
        'description' => 'Deskripsi lengkap kursi bar',
        'short_description' => 'Deskripsi singkat kursi bar',
        'price' => 750000,
        'material' => 'Kayu Mahoni',
        'stock' => 8,
        'status' => 'active',
        'sort_order' => 10,
    ]);

    $response = $this->put(route('admin.products.update', $product), [
        'category_id' => $this->category->id,
        'name' => 'Kursi Bar Updated',
        'slug' => 'kursi-bar',
        'short_description' => 'Deskripsi singkat kursi bar',
        'description' => 'Deskripsi lengkap kursi bar',
        'price' => '800.000',
        'stock' => 8,
        'material' => 'Kayu Mahoni',
        'status' => 'active',
        'sort_order' => 1,
    ]);

    $response->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'sort_order' => 1,
    ]);
});

test('updating product sort_order shifts other products in same category when moving up', function () {
    $this->actingAs($this->admin);

    // Product 1 (currently order 1)
    $product1 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'CT-BWL-002',
        'slug' => 'ct-bwl-002',
        'description' => 'Coffee table 2',
        'short_description' => 'Coffee table 2',
        'price' => 1470000,
        'stock' => 10,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    // Product 2 (currently order 2)
    $product2 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'CT-BWL-001',
        'slug' => 'ct-bwl-001',
        'description' => 'Coffee table 1',
        'short_description' => 'Coffee table 1',
        'price' => 1350000,
        'stock' => 118,
        'status' => 'active',
        'sort_order' => 2,
    ]);

    // Update Product 2 to sort_order = 1
    $response = $this->put(route('admin.products.update', $product2), [
        'category_id' => $this->category->id,
        'name' => 'CT-BWL-001',
        'slug' => 'ct-bwl-001',
        'short_description' => 'Coffee table 1',
        'description' => 'Coffee table 1',
        'material' => 'Kayu Jati',
        'price' => '1.350.000',
        'stock' => 118,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    $response->assertRedirect(route('admin.products.index'));

    // CT-BWL-001 should now be 1
    expect($product2->fresh()->sort_order)->toBe(1);
    // CT-BWL-002 should now be shifted to 2!
    expect($product1->fresh()->sort_order)->toBe(2);
});

test('updating product sort_order shifts other products in same category when moving down', function () {
    $this->actingAs($this->admin);

    $p1 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk 1',
        'slug' => 'produk-1',
        'description' => 'Desc 1',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 10,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    $p2 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk 2',
        'slug' => 'produk-2',
        'description' => 'Desc 2',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 10,
        'status' => 'active',
        'sort_order' => 2,
    ]);

    $p3 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk 3',
        'slug' => 'produk-3',
        'description' => 'Desc 3',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 10,
        'status' => 'active',
        'sort_order' => 3,
    ]);

    // Move p1 from 1 to 3
    $response = $this->put(route('admin.products.update', $p1), [
        'category_id' => $this->category->id,
        'name' => 'Produk 1',
        'slug' => 'produk-1',
        'description' => 'Desc 1',
        'material' => 'Kayu Jati',
        'price' => '100.000',
        'stock' => 10,
        'status' => 'active',
        'sort_order' => 3,
    ]);

    $response->assertRedirect(route('admin.products.index'));

    expect($p1->fresh()->sort_order)->toBe(3);
    expect($p2->fresh()->sort_order)->toBe(1);
    expect($p3->fresh()->sort_order)->toBe(2);
});

test('storing new product with existing sort_order shifts existing products in same category', function () {
    $this->actingAs($this->admin);

    $p1 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk Lama 1',
        'slug' => 'produk-lama-1',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    $p2 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk Lama 2',
        'slug' => 'produk-lama-2',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 2,
    ]);

    // Store new product at sort_order 1
    $response = $this->post(route('admin.products.store'), [
        'category_id' => $this->category->id,
        'name' => 'Produk Baru',
        'slug' => 'produk-baru',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => '150.000',
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
        'thumbnail' => UploadedFile::fake()->image('thumb.jpg'),
    ]);

    $response->assertRedirect(route('admin.products.index'));

    $newProduct = Product::where('slug', 'produk-baru')->first();
    expect($newProduct->sort_order)->toBe(1);
    expect($p1->fresh()->sort_order)->toBe(2);
    expect($p2->fresh()->sort_order)->toBe(3);
});

test('shifting sort_order only affects products in the same category', function () {
    $this->actingAs($this->admin);

    $cat2 = Category::create([
        'name' => 'Kategori Lain',
        'slug' => 'kategori-lain',
        'code' => 'KTL',
        'is_active' => true,
        'sort_order' => 2,
    ]);

    $cat1Product = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk Cat 1',
        'slug' => 'produk-cat-1',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    $cat2Product = Product::create([
        'category_id' => $cat2->id,
        'name' => 'Produk Cat 2',
        'slug' => 'produk-cat-2',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    // Store new product in cat 1 at sort_order 1
    $response = $this->post(route('admin.products.store'), [
        'category_id' => $this->category->id,
        'name' => 'Produk Cat 1 Baru',
        'slug' => 'produk-cat-1-baru',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => '100.000',
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
        'thumbnail' => UploadedFile::fake()->image('thumb.jpg'),
    ]);

    $response->assertRedirect(route('admin.products.index'));

    // Cat 1 product should shift to 2
    expect($cat1Product->fresh()->sort_order)->toBe(2);
    // Cat 2 product should REMAIN 1 (unaffected!)
    expect($cat2Product->fresh()->sort_order)->toBe(1);
});

test('quick update sort_order endpoint updates and shifts orders properly', function () {
    $this->actingAs($this->admin);

    $p1 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk A',
        'slug' => 'produk-a',
        'description' => 'Desc',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    $p2 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk B',
        'slug' => 'produk-b',
        'description' => 'Desc',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 2,
    ]);

    $response = $this->patchJson(route('admin.products.update-sort-order', $p2), [
        'sort_order' => 1,
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'sort_order' => 1,
        ]);

    expect($p2->fresh()->sort_order)->toBe(1);
    expect($p1->fresh()->sort_order)->toBe(2);
});

test('deleting a ranked product decrements higher sort_order in same category', function () {
    $this->actingAs($this->admin);

    $p1 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk A',
        'slug' => 'produk-a',
        'description' => 'Desc',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    $p2 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk B',
        'slug' => 'produk-b',
        'description' => 'Desc',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 2,
    ]);

    $p3 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk C',
        'slug' => 'produk-c',
        'description' => 'Desc',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 3,
    ]);

    // Delete p2 (which has sort_order 2)
    $this->delete(route('admin.products.destroy', $p2));

    expect($p1->fresh()->sort_order)->toBe(1);
    expect($p3->fresh()->sort_order)->toBe(2);
});

test('updating an unranked product (sort_order 0) to a rank shifts existing ranks up', function () {
    $this->actingAs($this->admin);

    $p1 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk A',
        'slug' => 'produk-a',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    $p2 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk B',
        'slug' => 'produk-b',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 2,
    ]);

    $pUnranked = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Produk Unranked',
        'slug' => 'produk-unranked',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 0,
    ]);

    // Assign pUnranked to rank 1
    $response = $this->patchJson(route('admin.products.update-sort-order', $pUnranked), [
        'sort_order' => 1,
    ]);

    $response->assertOk();

    expect($pUnranked->fresh()->sort_order)->toBe(1);
    expect($p1->fresh()->sort_order)->toBe(2);
    expect($p2->fresh()->sort_order)->toBe(3);
});

test('moving a product to another category shifts new category and closes gap in old category', function () {
    $this->actingAs($this->admin);

    $cat2 = Category::create([
        'name' => 'Kategori 2',
        'slug' => 'kategori-2',
        'code' => 'KT2',
        'is_active' => true,
        'sort_order' => 2,
    ]);

    // Cat 1 has P1 (1) and P2 (2)
    $p1 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'P1 Cat1',
        'slug' => 'p1-cat1',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    $p2 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'P2 Cat1',
        'slug' => 'p2-cat1',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 2,
    ]);

    // Cat 2 has P3 (1)
    $p3 = Product::create([
        'category_id' => $cat2->id,
        'name' => 'P3 Cat2',
        'slug' => 'p3-cat2',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    // Move p2 to Cat 2 with sort_order 1
    $this->put(route('admin.products.update', $p2), [
        'category_id' => $cat2->id,
        'name' => 'P2 Cat1',
        'slug' => 'p2-cat1',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => '100.000',
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    // In Cat 1: P1 remains 1
    expect($p1->fresh()->sort_order)->toBe(1);
    // In Cat 2: P2 is 1, P3 shifted to 2!
    expect($p2->fresh()->category_id)->toBe($cat2->id);
    expect($p2->fresh()->sort_order)->toBe(1);
    expect($p3->fresh()->sort_order)->toBe(2);
});

test('moving a product to 0 closes gaps in remaining ranked products', function () {
    $this->actingAs($this->admin);

    $p1 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'P1',
        'slug' => 'p1',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 1,
    ]);

    $p2 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'P2',
        'slug' => 'p2',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 2,
    ]);

    $p3 = Product::create([
        'category_id' => $this->category->id,
        'name' => 'P3',
        'slug' => 'p3',
        'description' => 'Desc',
        'material' => 'Kayu Jati',
        'price' => 100000,
        'stock' => 5,
        'status' => 'active',
        'sort_order' => 3,
    ]);

    // Move p2 to 0
    $this->patchJson(route('admin.products.update-sort-order', $p2), [
        'sort_order' => 0,
    ]);

    expect($p2->fresh()->sort_order)->toBe(0);
    expect($p1->fresh()->sort_order)->toBe(1);
    expect($p3->fresh()->sort_order)->toBe(2);
});


