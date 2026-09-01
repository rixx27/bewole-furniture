<?php

namespace App\Livewire\Frontend;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CartPage extends Component
{
    public array $cart = [];
    public int $subtotal = 0;

    public function mount(): void
    {
        $this->loadCart();
    }

    #[On('cart-updated')]
    public function loadCart(): void
    {
        $cartService = app(CartService::class);
        $this->cart = $cartService->getCart();
        $this->subtotal = $cartService->getSubtotal();
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $cartService = app(CartService::class);
        $this->cart = $cartService->updateQuantity($productId, $quantity);
        $this->subtotal = $cartService->getSubtotal();
        $count = $cartService->getItemCount();

        $this->dispatch('cart-updated', count: $count);
    }

    public function removeItem(int $productId): void
    {
        $cartService = app(CartService::class);
        $this->cart = $cartService->remove($productId);
        $this->subtotal = $cartService->getSubtotal();
        $count = $cartService->getItemCount();

        $this->dispatch('cart-updated', count: $count);
        $this->dispatch('notify', message: 'Item berhasil dihapus dari keranjang.');
    }

    public function proceedToCheckout()
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', message: 'Keranjang Anda masih kosong.');
            return;
        }

        if (!Auth::check()) {
            session(['url.intended' => route('checkout.index')]);
            return redirect()->route('login');
        }

        return redirect()->route('checkout.index');
    }

    public function render()
    {
        $this->loadCart();
        return view('livewire.frontend.cart-page');
    }
}
