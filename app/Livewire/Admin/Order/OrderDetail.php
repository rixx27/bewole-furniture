<?php

namespace App\Livewire\Admin\Order;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\On;

class OrderDetail extends Component
{
    public ?Order $order = null;
    public bool $show = false;
    public ?string $customPriceInput = null;

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

        if ($this->order && $this->order->is_custom) {
            $this->customPriceInput = (float) $this->order->total_price > 0
                ? (string) (int) $this->order->total_price
                : '';
        } else {
            $this->customPriceInput = null;
        }

        $this->show = true;
    }

    public function saveCustomPrice(): void
    {
        if (!$this->order || !$this->order->is_custom) {
            return;
        }

        $price = (float) preg_replace('/[^0-9]/', '', (string) $this->customPriceInput);
        if ($price <= 0) {
            $this->dispatch('notify', type: 'error', message: 'Nominal harga harus lebih besar dari 0.');
            return;
        }

        $oldPrice = (float) $this->order->total_price;
        $this->order->total_price = $price;
        $this->order->save();

        $this->order->statusHistories()->create([
            'status' => $this->order->status,
            'description' => 'Admin menetapkan kesepakatan harga custom furniture: Rp ' . number_format($price, 0, ',', '.') . ($oldPrice > 0 ? ' (Sebelumnya: Rp ' . number_format($oldPrice, 0, ',', '.') . ')' : '') . '.',
            'changed_by' => auth()->id(),
        ]);

        $this->order->refresh();
        $this->dispatch('orderUpdated');
        $this->dispatch('notify', type: 'success', message: 'Estimasi harga custom furniture berhasil diperbarui.');
    }

    #[On('closeModal')]
    public function close(): void
    {
        $this->show = false;
        $this->order = null;
        $this->customPriceInput = null;
    }

    public function render()
    {
        return view('livewire.admin.order.order-detail');
    }
}
