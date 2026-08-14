<?php

namespace App\Livewire\Admin\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use Livewire\Component;
use Livewire\Attributes\On;

class OrderStatusManager extends Component
{
    public ?Order $order = null;
    public bool $show = false;
    public ?string $newStatus = null;
    public ?string $notes = null;
    public array $availableStatuses = [];

    public function mount(?int $orderId = null): void
    {
        if ($orderId) {
            $this->loadOrder($orderId);
        }
    }

    protected $rules = [
        'newStatus' => 'required',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'newStatus.required' => 'Status wajib dipilih.',
    ];

    #[On('openStatus')]
    public function loadOrder(int $orderId): void
    {
        $this->order = Order::find($orderId);
        $this->availableStatuses = [];
        $this->newStatus = null;
        $this->notes = null;

        if ($this->order) {
            foreach (OrderStatus::cases() as $status) {
                if ($status->value !== $this->order->status) {
                    $this->availableStatuses[] = [
                        'value' => $status->value,
                        'label' => $status->label(),
                        'emoji' => $status->emoji(),
                        'description' => $status->description(),
                        'color' => $status->color(),
                    ];
                }
            }
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

    public function updateStatus(OrderService $orderService): void
    {
        $this->validate();

        if (!$this->order || !$this->newStatus) {
            return;
        }

        try {
            $targetStatus = OrderStatus::tryFrom($this->newStatus);
            if (!$targetStatus) {
                $this->addError('newStatus', 'Status tidak valid.');
                return;
            }

            $orderService->updateStatus($this->order, $targetStatus, $this->notes);

            $this->dispatch('orderUpdated');
            $this->dispatch('notify', type: 'success', message: "Status pesanan berhasil diubah menjadi {$targetStatus->label()}.");
            $this->close();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.order.order-status-manager');
    }
}
