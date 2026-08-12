<?php

namespace App\Livewire\Frontend;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;
    public int $quantity = 1;
    public ?string $selectedImage = null;

    public function mount(Product $product): void
    {
        $this->product = $product->load(['category', 'images', 'visibleReviews']);
        $this->selectedImage = $this->product->thumbnail;
    }

    public function incrementQuantity(): void
    {
        if ($this->product->stock > 0 && $this->quantity >= $this->product->stock) {
            return;
        }
        $this->quantity++;
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function selectImage(string $imagePath): void
    {
        $this->selectedImage = $imagePath;
    }

    public function addToCart(): void
    {
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, $this->quantity);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: "{$this->product->name} ({$this->quantity}x) berhasil ditambahkan ke keranjang!");
    }

    public function buyNow()
    {
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, $this->quantity);

        $this->dispatch('cart-updated');

        return redirect()->route('cart.index');
    }

    public function render()
    {
        return view('livewire.frontend.product-detail');
    }
}
