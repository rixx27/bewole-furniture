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
    public ?string $down_payment_amount = null;
    public ?string $rejection_reason = null;
    public ?string $notes = null;

    public function mount(?int $orderId = null): void
    {
        if ($orderId) {
            $this->loadOrder($orderId);
        }
    }

    protected function rules(): array
    {
        return [
            'payment_status' => 'required|in:unpaid,down_payment,paid,failed,refunded',
            'down_payment_amount' => 'required_if:payment_status,down_payment|nullable|string',
            'rejection_reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'payment_status.required' => 'Status pembayaran wajib dipilih.',
        'payment_status.in' => 'Status pembayaran tidak valid.',
        'down_payment_amount.required_if' => 'Nominal DP wajib diisi jika memilih status DP (Uang Muka).',
        'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter.',
        'notes.max' => 'Catatan maksimal 1000 karakter.',
    ];

    #[On('openPayment')]
    public function loadOrder(int $orderId): void
    {
        $this->order = Order::find($orderId);
        $this->payment_status = $this->order?->payment_status;
        $this->rejection_reason = $this->order?->payment_rejection_reason;
        $this->notes = null;

        if ($this->order) {
            $dpVal = $this->order->down_payment_amount > 0 
                ? (float) $this->order->down_payment_amount 
                : round((float) $this->order->total_price * 0.5);
            $this->down_payment_amount = number_format($dpVal, 0, ',', '.');
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

            $parsedDp = null;
            if ($paymentStatus === PaymentStatus::DownPayment && $this->down_payment_amount) {
                $cleaned = str_replace(['.', ','], ['', '.'], $this->down_payment_amount);
                $parsedDp = (float) $cleaned;
            }

            $orderService->updatePayment(
                $this->order,
                $paymentStatus,
                $this->notes,
                $parsedDp,
                $this->rejection_reason
            );

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
