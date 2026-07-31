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
            $currentStatus = OrderStatus::tryFrom($this->order->status);
            if ($currentStatus) {
                foreach (OrderStatus::cases() as $status) {
                    if ($currentStatus->canTransitionTo($status)) {
                        $this->availableStatuses[] = $status;
                    }
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
