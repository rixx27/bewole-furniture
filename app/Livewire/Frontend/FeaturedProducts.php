<?php

namespace App\Livewire\Frontend;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Component;

class FeaturedProducts extends Component
{
    public string $title = 'Featured Pieces';
    public string $badge = 'Pilihan Unggulan';
    public string $subtitle = 'Koleksi pilihan karya furniture unggulan Bewole, dipadukan dengan material premium dan pengerjaan tangan yang elegan.';
    public int $limit = 8;

    public function addToCart(int $productId): void
    {
        $product = Product::active()->with('category')->find($productId);
        if (!$product) {
            $this->dispatch('notify', message: 'Produk tidak ditemukan atau tidak tersedia.', type: 'error');
            return;
        }

        $cartService = app(CartService::class);
        $cartService->add($productId, 1);
        $count = $cartService->getItemCount();
        $formattedPrice = $product->formatted_discount_price ?: $product->formatted_price;

        $this->dispatch('cart-updated', count: $count, product: [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (int) ($product->discount_price ?? $product->price),
            'formatted_price' => $formattedPrice,
            'thumbnail' => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
            'quantity' => 1,
        ]);

        $this->dispatch('notify', 
            message: "{$product->name} berhasil ditambahkan ke keranjang!",
            type: 'success',
            product_name: $product->name,
            product_thumbnail: $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
            product_price: $formattedPrice,
            product_quantity: 1,
            cart_count: $count
        );
    }

    public function render()
    {
        $products = Product::query()
            ->with('category')
            ->active()
            ->sorted()
            ->when(
                Product::query()->active()->featured()->exists(),
                fn ($q) => $q->featured()
            )
            ->limit($this->limit)
            ->get();

        return view('livewire.frontend.featured-products', [
            'products' => $products,
        ]);
    }
}
