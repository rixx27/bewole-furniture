<?php

namespace App\Livewire\Admin\Order;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderPayment as OrderPaymentModel;
use App\Services\OrderService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

class OrderPayment extends Component
{
    use WithFileUploads;

    public ?Order $order = null;
    public bool $show = false;
    public ?string $payment_status = null;
    public ?string $down_payment_amount = null;
    public ?string $rejection_reason = null;
    public ?string $notes = null;

    // Installment action states
    public ?int $verifyingPaymentId = null;
    public ?string $verify_amount = null;
    public ?string $verify_notes = null;

    public ?int $rejectingPaymentId = null;
    public ?string $reject_reason = null;

    // Manual payment addition
    public bool $showManualForm = false;
    public ?string $manual_amount = null;
    public string $manual_method = 'cash'; // 'cash', 'bank_transfer', 'qris'
    public ?string $manual_notes = null;
    public $manual_proof = null;

    public ?string $previewImage = null;
    public ?string $previewTitle = null;

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
        $this->order = Order::with(['payments.verifiedBy'])->find($orderId);
        $this->payment_status = $this->order?->payment_status;
        $this->rejection_reason = $this->order?->payment_rejection_reason;
        $this->notes = null;

        $this->verifyingPaymentId = null;
        $this->verify_amount = null;
        $this->verify_notes = null;

        $this->rejectingPaymentId = null;
        $this->reject_reason = null;

        $this->showManualForm = false;
        $this->manual_amount = null;
        $this->manual_method = 'cash';
        $this->manual_notes = null;
        $this->manual_proof = null;

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

    public function startVerifyInstallment(int $paymentId): void
    {
        $this->verifyingPaymentId = $paymentId;
        $this->rejectingPaymentId = null;

        $payment = OrderPaymentModel::find($paymentId);
        if ($payment && $this->order) {
            // Suggest remaining payment or current payment amount if set
            $suggested = (float) $payment->amount > 0 
                ? (float) $payment->amount 
                : ($this->order->remaining_payment > 0 ? (float) $this->order->remaining_payment : (float) $this->order->total_price);
            $this->verify_amount = number_format($suggested, 0, ',', '.');
            $this->verify_notes = null;
        }
    }

    public function cancelVerifyInstallment(): void
    {
        $this->verifyingPaymentId = null;
        $this->verify_amount = null;
        $this->verify_notes = null;
    }

    public function confirmVerifyInstallment(OrderService $orderService): void
    {
        $this->validate([
            'verify_amount' => 'required|string',
            'verify_notes' => 'nullable|string|max:500',
        ], [
            'verify_amount.required' => 'Nominal pembayaran yang masuk ke rekening wajib diisi.',
        ]);

        $payment = OrderPaymentModel::find($this->verifyingPaymentId);
        if (!$payment) {
            $this->dispatch('notify', type: 'error', message: 'Data pembayaran tidak ditemukan.');
            return;
        }

        $cleaned = str_replace(['.', ','], ['', '.'], $this->verify_amount);
        $amount = (float) $cleaned;

        if ($amount <= 0) {
            $this->addError('verify_amount', 'Nominal transfer harus lebih besar dari Rp 0.');
            return;
        }

        try {
            $this->order = $orderService->verifyPaymentInstallment($payment, $amount, $this->verify_notes);
            $this->payment_status = $this->order->payment_status;
            $this->cancelVerifyInstallment();

            $this->dispatch('orderUpdated');
            $this->dispatch('notify', type: 'success', message: "Pembayaran #{$payment->payment_number} berhasil diverifikasi!");
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memverifikasi: ' . $e->getMessage());
        }
    }

    public function startRejectInstallment(int $paymentId): void
    {
        $this->rejectingPaymentId = $paymentId;
        $this->verifyingPaymentId = null;
        $this->reject_reason = null;
    }

    public function cancelRejectInstallment(): void
    {
        $this->rejectingPaymentId = null;
        $this->reject_reason = null;
    }

    public function confirmRejectInstallment(OrderService $orderService): void
    {
        $this->validate([
            'reject_reason' => 'required|string|max:500',
        ], [
            'reject_reason.required' => 'Alasan penolakan bukti pembayaran wajib diisi.',
        ]);

        $payment = OrderPaymentModel::find($this->rejectingPaymentId);
        if (!$payment) {
            $this->dispatch('notify', type: 'error', message: 'Data pembayaran tidak ditemukan.');
            return;
        }

        try {
            $this->order = $orderService->rejectPaymentInstallment($payment, $this->reject_reason);
            $this->payment_status = $this->order->payment_status;
            $this->cancelRejectInstallment();

            $this->dispatch('orderUpdated');
            $this->dispatch('notify', type: 'warning', message: "Bukti pembayaran #{$payment->payment_number} telah ditolak.");
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menolak: ' . $e->getMessage());
        }
    }

    public function toggleManualForm(): void
    {
        $this->showManualForm = !$this->showManualForm;
        if ($this->showManualForm && $this->order) {
            $suggested = $this->order->remaining_payment > 0 ? (float) $this->order->remaining_payment : 0;
            $this->manual_amount = number_format($suggested, 0, ',', '.');
        }
    }

    public function saveManualPayment(OrderService $orderService): void
    {
        $this->validate([
            'manual_amount' => 'required|string',
            'manual_method' => 'required|in:cash,bank_transfer,qris',
            'manual_notes' => 'nullable|string|max:500',
            'manual_proof' => 'nullable|image|max:5120',
        ], [
            'manual_amount.required' => 'Nominal pembayaran manual wajib diisi.',
            'manual_proof.image' => 'File bukti harus berupa gambar.',
        ]);

        $cleaned = str_replace(['.', ','], ['', '.'], $this->manual_amount);
        $amount = (float) $cleaned;

        if ($amount <= 0) {
            $this->addError('manual_amount', 'Nominal harus lebih dari Rp 0.');
            return;
        }

        try {
            $proofPath = null;
            if ($this->manual_proof) {
                $filename = 'proof_manual_' . $this->order->order_code . '_' . time() . '.' . $this->manual_proof->getClientOriginalExtension();
                $proofPath = $this->manual_proof->storeAs('payment_proofs', $filename, 'public');
            }

            $orderService->addManualPayment(
                $this->order,
                $amount,
                $this->manual_method,
                $this->manual_notes,
                $proofPath
            );

            $this->order->refresh();
            $this->payment_status = $this->order->payment_status;
            $this->showManualForm = false;
            $this->reset(['manual_amount', 'manual_notes', 'manual_proof']);

            $this->dispatch('orderUpdated');
            $this->dispatch('notify', type: 'success', message: 'Pembayaran manual berhasil dicatat!');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }
    }

    public function openPreview(string $url, string $title): void
    {
        $this->previewImage = $url;
        $this->previewTitle = $title;
    }

    public function closePreview(): void
    {
        $this->previewImage = null;
        $this->previewTitle = null;
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
        $payments = $this->order ? $this->order->payments()->with('verifiedBy')->get() : collect();

        return view('livewire.admin.order.order-payment', [
            'payments' => $payments,
        ]);
    }
}
