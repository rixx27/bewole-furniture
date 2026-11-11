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

        if ($this->order && ($this->order->is_custom || (float) $this->order->total_price <= 0 || ($this->order->product && !$this->order->product->show_price))) {
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
        if (!$this->order) {
            return;
        }

        $price = (float) preg_replace('/[^0-9]/', '', (string) $this->customPriceInput);
        if ($price <= 0) {
            $this->dispatch('notify', type: 'error', message: 'Nominal harga harus lebih besar dari 0.');
            return;
        }

        $oldPrice = (float) ($this->order->product_subtotal ?: $this->order->total_price);
        $this->order->product_subtotal = $price;
        if ($this->order->shipping_cost_type === 'billed_to_order') {
            $this->order->total_price = $price + (float) $this->order->shipping_cost;
        } else {
            $this->order->total_price = $price;
        }

        if ($this->order->items && $this->order->items->count() > 0) {
            foreach ($this->order->items as $item) {
                $itemQty = max(1, $item->quantity);
                $item->unit_price = (int) round($price / $itemQty);
                $item->total_price = (int) $price;
                $item->save();
            }
        }

        $this->order->save();

        $priceDescription = $this->order->is_custom
            ? 'Admin menetapkan kesepakatan harga custom furniture: Rp ' . number_format($price, 0, ',', '.') . ($oldPrice > 0 ? ' (Sebelumnya: Rp ' . number_format($oldPrice, 0, ',', '.') . ')' : '') . '.'
            : 'Admin menetapkan kesepakatan harga produk: Rp ' . number_format($price, 0, ',', '.') . ($oldPrice > 0 ? ' (Sebelumnya: Rp ' . number_format($oldPrice, 0, ',', '.') . ')' : '') . '.';

        $this->order->statusHistories()->create([
            'status' => $this->order->status,
            'description' => $priceDescription,
            'changed_by' => auth()->id(),
        ]);

        $this->order->refresh();
        $this->dispatch('orderUpdated');
        $this->dispatch('notify', type: 'success', message: 'Kesepakatan harga berhasil diperbarui.');
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
