<?php

namespace App\Livewire\Admin\Order;

use App\Enums\ShippingMethod;
use App\Models\Order;
use App\Services\OrderService;
use Livewire\Component;
use Livewire\Attributes\On;

class OrderShipping extends Component
{
    public ?Order $order = null;
    public bool $show = false;

    // Shipping fields
    public ?string $shipping_method = null;
    public ?string $courier = null;
    public ?string $tracking_number = null;
    public ?string $driver_name = null;
    public ?string $vehicle_number = null;
    public ?string $shipping_date = null;
    public ?string $pickup_date = null;

    public function mount(?int $orderId = null): void
    {
        if ($orderId) {
            $this->loadOrder($orderId);
        }
    }

    protected function rules(): array
    {
        $rules = [
            'shipping_method' => 'required|in:expedition,internal_delivery,self_pickup',
        ];

        if ($this->shipping_method === ShippingMethod::Expedition->value) {
            $rules['courier'] = 'required|string|max:255';
            $rules['tracking_number'] = 'required|string|max:255';
            $rules['shipping_date'] = 'required|date';
        } elseif ($this->shipping_method === ShippingMethod::InternalDelivery->value) {
            $rules['driver_name'] = 'required|string|max:255';
            $rules['vehicle_number'] = 'required|string|max:255';
            $rules['shipping_date'] = 'required|date';
        } elseif ($this->shipping_method === ShippingMethod::SelfPickup->value) {
            $rules['pickup_date'] = 'required|date|after_or_equal:today';
        }

        return $rules;
    }

    protected $messages = [
        'shipping_method.required' => 'Metode pengiriman wajib dipilih.',
        'courier.required' => 'Nama kurir wajib diisi.',
        'courier.max' => 'Nama kurir maksimal 255 karakter.',
        'tracking_number.required' => 'Nomor resi wajib diisi.',
        'tracking_number.max' => 'Nomor resi maksimal 255 karakter.',
        'shipping_date.required' => 'Tanggal kirim wajib diisi.',
        'shipping_date.date' => 'Tanggal kirim tidak valid.',
        'driver_name.required' => 'Nama driver wajib diisi.',
        'driver_name.max' => 'Nama driver maksimal 255 karakter.',
        'vehicle_number.required' => 'Nomor kendaraan wajib diisi.',
        'vehicle_number.max' => 'Nomor kendaraan maksimal 255 karakter.',
        'pickup_date.required' => 'Tanggal pengambilan wajib diisi.',
        'pickup_date.date' => 'Tanggal pengambilan tidak valid.',
        'pickup_date.after_or_equal' => 'Tanggal pengambilan minimal hari ini.',
    ];

    #[On('openShipping')]
    public function loadOrder(int $orderId): void
    {
        $this->order = Order::find($orderId);
        $this->reset(['shipping_method', 'courier', 'tracking_number', 'driver_name', 'vehicle_number', 'shipping_date', 'pickup_date']);

        if ($this->order) {
            $this->shipping_method = $this->order->shipping_method;
            $this->courier = $this->order->courier;
            $this->tracking_number = $this->order->tracking_number;
            $this->driver_name = $this->order->driver_name;
            $this->vehicle_number = $this->order->vehicle_number;
            $this->shipping_date = $this->order->shipping_date?->format('Y-m-d');
            $this->pickup_date = $this->order->pickup_date?->format('Y-m-d');
        }

        $this->show = true;
    }

    #[On('closeModal')]
    public function close(): void
    {
        $this->show = false;
        $this->order = null;
        $this->resetValidation();
    }

    public function updatedShippingMethod(): void
    {
        $this->reset(['courier', 'tracking_number', 'driver_name', 'vehicle_number', 'shipping_date', 'pickup_date']);
        $this->resetValidation();
    }

    public function saveShipping(OrderService $orderService): void
    {
        $this->validate();

        if (!$this->order) {
            return;
        }

        try {
            $data = [
                'shipping_method' => $this->shipping_method,
                'courier' => $this->courier,
                'tracking_number' => $this->tracking_number,
                'driver_name' => $this->driver_name,
                'vehicle_number' => $this->vehicle_number,
                'shipping_date' => $this->shipping_date,
                'pickup_date' => $this->pickup_date,
            ];

            $orderService->updateShipping($this->order, $data);

            $this->dispatch('orderUpdated');
            $this->dispatch('notify', type: 'success', message: 'Informasi pengiriman berhasil diperbarui.');
            $this->close();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.order.order-shipping');
    }
}
