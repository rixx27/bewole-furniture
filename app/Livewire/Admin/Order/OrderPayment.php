<?php

namespace App\Livewire\Admin\Order;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\OrderService;
use Livewire\Component;
use Livewire\Attributes\On;

class OrderPayment extends Component
{
    public ?Order $order = null;
    public bool $show = false;
    public ?string $payment_status = null;
    public ?string $notes = null;

    public function mount(?int $orderId = null): void
    {
        if ($orderId) {
            $this->loadOrder($orderId);
        }
    }

    protected $rules = [
        'payment_status' => 'required|in:unpaid,paid,failed,refunded',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'payment_status.required' => 'Status pembayaran wajib dipilih.',
        'notes.max' => 'Catatan maksimal 1000 karakter.',
    ];

    #[On('openPayment')]
    public function loadOrder(int $orderId): void
    {
        $this->order = Order::find($orderId);
        $this->payment_status = $this->order?->payment_status;
        $this->notes = null;
        $this->show = true;
    }

    #[On('closeModal')]
    public function close(): void
    {
        $this->show = false;
        $this->order = null;
        $this->resetValidation();
    }

    public function updatePayment(OrderService $orderService): void
    {
        $this->validate();

        if (!$this->order || !$this->payment_status) {
            return;
        }

        try {
            $paymentStatus = PaymentStatus::tryFrom($this->payment_status);
            if (!$paymentStatus) {
                $this->addError('payment_status', 'Status pembayaran tidak valid.');
                return;
            }

            $orderService->updatePayment($this->order, $paymentStatus, $this->notes);

            $this->dispatch('orderUpdated');
            $this->dispatch('notify', type: 'success', message: "Status pembayaran berhasil diubah menjadi {$paymentStatus->label()}.");
            $this->close();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.order.order-payment');
    }
}
