<?php

namespace App\Livewire\Frontend;

use App\Models\Order;
use Livewire\Attributes\Url;
use Livewire\Component;

class OrderTracking extends Component
{
    #[Url(as: 'code')]
    public string $searchCode = '';

    public ?Order $order = null;
    public bool $searched = false;

    public function mount(?string $orderCode = null): void
    {
        if ($orderCode) {
            $this->searchCode = $orderCode;
        }

        if (!empty($this->searchCode)) {
            $this->trackOrder();
        }
    }

    public function trackOrder(): void
    {
        $code = trim($this->searchCode);

        if (empty($code)) {
            $this->order = null;
            $this->searched = false;
            return;
        }

        $this->order = Order::with(['product', 'statusHistories'])
            ->where('order_code', $code)
            ->first();

        $this->searched = true;
    }

    public function render()
    {
        return view('livewire.frontend.order-tracking');
    }
}
