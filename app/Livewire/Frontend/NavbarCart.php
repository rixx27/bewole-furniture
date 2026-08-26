<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use Livewire\Attributes\On;

class NavbarCart extends Component
{
    public int $cartQty = 0;

    public function mount(): void
    {
        $this->loadCount();
    }

    #[On('cart-updated')]
    public function loadCount(): void
    {
        $this->cartQty = collect(session('bewole_cart', []))->sum('quantity');
    }

    public function render()
    {
        return view('livewire.frontend.navbar-cart');
    }
}
