<?php

namespace App\Livewire\Frontend;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class NavbarCart extends Component
{
    public int $cartQty = 0;

    public function mount(): void
    {
        $this->loadCount();
    }

    #[On('cart-updated')]
    public function loadCount($count = null): void
    {
        if (is_numeric($count)) {
            $this->cartQty = (int) $count;
        } else {
            $this->cartQty = app(CartService::class)->getItemCount();
        }
    }

    public function render()
    {
        return view('livewire.frontend.navbar-cart');
    }
}
