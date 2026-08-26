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
            $currentEnum = OrderStatus::tryFrom($this->order->status);

            foreach (OrderStatus::cases() as $status) {
                $canSelect = $currentEnum ? $currentEnum->canTransitionTo($status) : false;
                $isCurrent = $this->order->status === $status->value;

                $this->availableStatuses[] = [
                    'value' => $status->value,
                    'label' => $status->label(),
                    'description' => $status->description(),
                    'color' => $status->color(),
                    'isCurrent' => $isCurrent,
                    'canSelect' => $canSelect,
                ];

                if ($canSelect && !$this->newStatus && $status->value !== OrderStatus::Cancelled->value) {
                    $this->newStatus = $status->value;
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
            $currentEnum = OrderStatus::tryFrom($this->order->status);
            $targetStatus = OrderStatus::tryFrom($this->newStatus);

            if (!$targetStatus) {
                $this->addError('newStatus', 'Status tidak valid.');
                return;
            }

            if (!$currentEnum || !$currentEnum->canTransitionTo($targetStatus)) {
                $this->addError('newStatus', 'Status pesanan hanya dapat dilanjutkan ke tahap berikutnya atau dibatalkan.');
                return;
            }

            $orderService->updateStatus($this->order, $targetStatus, $this->notes);

            $this->dispatch('orderUpdated');
            $this->dispatch('notify', type: 'success', message: "Status pesanan berhasil diubah menjadi {$targetStatus->label()}.");
            $this->close();
        } catch (\Exception $e) {
            $this->addError('newStatus', $e->getMessage());
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.order.order-status-manager');
    }
}
