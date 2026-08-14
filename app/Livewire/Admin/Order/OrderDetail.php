<?php

namespace App\Livewire\Admin\Order;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\On;

class OrderDetail extends Component
{
    public ?Order $order = null;
    public bool $show = false;

    public function mount(?int $orderId = null): void
    {
        if ($orderId) {
            $this->loadOrder($orderId);
        }
    }

    #[On('openDetail')]
    public function loadOrder(int $orderId): void
    {
        $this->order = Order::with([
            'user',
            'product',
            'product.images',
            'statusHistories',
            'statusHistories.changedBy',
            'review',
        ])->find($orderId);

        $this->show = true;
    }

    #[On('closeModal')]
    public function close(): void
    {
        $this->show = false;
        $this->order = null;
    }

    public function render()
    {
        return view('livewire.admin.order.order-detail');
    }
}
