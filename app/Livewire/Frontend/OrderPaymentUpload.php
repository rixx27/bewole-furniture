<?php

namespace App\Livewire\Frontend;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class OrderPaymentUpload extends Component
{
    use WithFileUploads;

    public int $orderId;
    public $proof;
    public ?string $customer_notes = null;
    public string $payment_type = 'dp'; // 'dp' or 'full'
    public ?string $dp_amount = null;
    public bool $is_final_payment = false;
    public bool $showForm = false;
    public ?string $previewModalImage = null;
    public ?string $previewModalTitle = null;

    protected function rules(): array
    {
        return [
            'proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB
            'customer_notes' => 'nullable|string|max:500',
            'payment_type' => 'nullable|in:dp,full',
            'dp_amount' => 'nullable|string',
        ];
    }

    protected $messages = [
        'proof.required' => 'Silakan pilih foto bukti transfer pembayaran.',
        'proof.image' => 'File bukti transfer harus berupa gambar.',
        'proof.mimes' => 'Format file yang didukung: JPG, JPEG, PNG, WEBP.',
        'proof.max' => 'Ukuran foto maksimal 5MB.',
        'customer_notes.max' => 'Catatan maksimal 500 karakter.',
    ];

    public function mount(int $orderId, bool $isFinalPayment = false): void
    {
        $this->orderId = $orderId;
        $this->is_final_payment = $isFinalPayment;

        $order = Order::find($this->orderId);
        if ($order) {
            $half = round((float) $order->total_price * 0.5);
            $this->dp_amount = number_format($half, 0, ',', '.');
        }
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        $this->reset(['proof', 'customer_notes']);
        $this->resetValidation();
    }

    public function openImagePreview(string $url, string $title): void
    {
        $this->previewModalImage = $url;
        $this->previewModalTitle = $title;
    }

    public function closeImagePreview(): void
    {
        $this->previewModalImage = null;
        $this->previewModalTitle = null;
    }

    public function uploadPaymentProof(OrderService $orderService)
    {
        $this->validate();

        $order = Order::with('payments')->find($this->orderId);
        if (!$order) {
            $this->dispatch('notify', type: 'error', message: 'Pesanan tidak ditemukan.');
            return;
        }

        // Check authentication / ownership
        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $nextNumber = $order->next_payment_number;
            $filename = 'proof_' . $order->order_code . '_termin_' . $nextNumber . '_' . time() . '.' . $this->proof->getClientOriginalExtension();
            $path = $this->proof->storeAs('payment_proofs', $filename, 'public');

            // Attach proof via OrderService
            $orderService->attachPaymentProof($order, $path, $this->is_final_payment, $this->customer_notes);

            $this->reset(['proof', 'customer_notes', 'showForm']);
            $this->dispatch('paymentProofUploaded');
            $this->dispatch('notify', type: 'success', message: "Bukti pembayaran ke-{$nextNumber} berhasil diunggah!");

            // Refresh page/component
            return redirect(request()->header('Referer') ?: route('orders.show', $order));
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal mengunggah bukti transfer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $order = Order::with(['payments.verifiedBy'])->find($this->orderId);
        $payments = $order ? $order->payments : collect();

        return view('livewire.frontend.order-payment-upload', [
            'order' => $order,
            'payments' => $payments,
            'nextPaymentNumber' => $order ? $order->next_payment_number : 1,
        ]);
    }
}
