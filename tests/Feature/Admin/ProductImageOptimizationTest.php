<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\ImageOptimizerService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Storage::fake('public');
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->category = Category::create([
        'name' => 'Kursi Kayu',
        'slug' => 'kursi-kayu',
        'code' => 'KK',
        'is_active' => true,
        'sort_order' => 1,
    ]);
});

test('image optimizer compresses and stores image as webp', function () {
    $file = UploadedFile::fake()->image('sofa.jpg', 1600, 1200);

    $storedPath = ImageOptimizerService::compressAndStore($file, 'products/thumbnails', 1200, 1200, 82);

    expect($storedPath)->toEndWith('.webp');
    Storage::disk('public')->assertExists($storedPath);
});

test('storing product auto compresses thumbnail and gallery images to webp', function () {
    $this->actingAs($this->admin);

    $response = $this->post(route('admin.products.store'), [
        'category_id' => $this->category->id,
        'name' => 'Kursi Cafe Modern',
        'slug' => 'kursi-cafe-modern',
        'short_description' => 'Kursi kayu minimalis',
        'description' => 'Deskripsi lengkap kursi kayu',
        'price' => '1.200.000',
        'stock' => 15,
        'material' => 'Kayu Jati',
        'status' => 'active',
        'thumbnail' => UploadedFile::fake()->image('thumb.png', 1500, 1500),
        'gallery' => [
            UploadedFile::fake()->image('gallery1.jpg', 1800, 1200),
            UploadedFile::fake()->image('gallery2.jpg', 1200, 800),
        ],
    ]);

    $response->assertRedirect(route('admin.products.index'));

    $product = Product::with('images')->where('slug', 'kursi-cafe-modern')->first();
    expect($product)->not->toBeNull();
    expect($product->thumbnail)->toEndWith('.webp');
    Storage::disk('public')->assertExists($product->thumbnail);

    expect($product->images)->toHaveCount(2);
    foreach ($product->images as $galleryImg) {
        expect($galleryImg->image)->toEndWith('.webp');
        Storage::disk('public')->assertExists($galleryImg->image);
    }
});

test('updating product thumbnail compresses new thumbnail to webp and deletes old one', function () {
    $this->actingAs($this->admin);

    // Initial product
    $initialThumb = ImageOptimizerService::compressAndStore(
        UploadedFile::fake()->image('old.jpg', 800, 800),
        'products/thumbnails'
    );

    $product = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Meja Kerja Minimalis',
        'slug' => 'meja-kerja-minimalis',
        'description' => 'Deskripsi meja',
        'short_description' => 'Deskripsi singkat',
        'price' => 2000000,
        'material' => 'Kayu Jati',
        'stock' => 5,
        'status' => 'active',
        'thumbnail' => $initialThumb,
    ]);

    Storage::disk('public')->assertExists($initialThumb);

    // Update with new thumbnail
    $response = $this->put(route('admin.products.update', $product), [
        'category_id' => $this->category->id,
        'name' => 'Meja Kerja Minimalis New',
        'slug' => 'meja-kerja-minimalis',
        'description' => 'Deskripsi meja',
        'short_description' => 'Deskripsi singkat',
        'price' => '2.000.000',
        'material' => 'Kayu Jati',
        'stock' => 5,
        'status' => 'active',
        'thumbnail' => UploadedFile::fake()->image('new.jpg', 1400, 1000),
    ]);

    $response->assertRedirect(route('admin.products.index'));

    $product->refresh();
    expect($product->thumbnail)->toEndWith('.webp');
    expect($product->thumbnail)->not->toBe($initialThumb);
    Storage::disk('public')->assertExists($product->thumbnail);
    Storage::disk('public')->assertMissing($initialThumb);
});
