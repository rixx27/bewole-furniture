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
