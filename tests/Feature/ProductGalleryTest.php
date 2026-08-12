<?php

use App\Models\Category;
use App\Models\Product;
use Livewire\Livewire;

it('renders gallery images using the stored image path column', function () {
    $category = Category::create([
        'name' => 'Kursi',
        'slug' => 'kursi',
        'code' => 'CAT001',
        'short_description' => 'Kategori kursi',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Kursi Tamu',
        'slug' => 'kursi-tamu',
        'description' => 'Deskripsi kursi',
        'short_description' => 'Kursi kayu',
        'price' => 300000,
        'discount_percentage' => null,
        'discount_price' => 300000,
        'material' => 'Rotan',
        'dimensions' => '80x70x80',
        'weight' => 12,
        'thumbnail' => 'products/thumbnails/sample.jpg',
        'stock' => 12,
        'status' => 'active',
    ]);

    $image = $product->images()->create([
        'image' => 'products/gallery/sample-gallery.jpg',
        'is_primary' => false,
        'sort_order' => 1,
    ]);

    Livewire::test(\App\Livewire\Frontend\ProductDetail::class, ['product' => $product])
        ->assertSee(asset('storage/' . $image->image));
});
