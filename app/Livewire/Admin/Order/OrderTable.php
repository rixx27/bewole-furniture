<?php

namespace App\Livewire\Admin\Order;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingMethod;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;

class OrderTable extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $statusFilter = '';

    #[Url(history: true)]
    public string $paymentFilter = '';

    #[Url(history: true)]
    public string $shippingFilter = '';

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    public ?int $selectedOrderId = null;
    public bool $showDetailModal = false;
    public bool $showStatusModal = false;
    public bool $showShippingModal = false;
    public bool $showPaymentModal = false;
    public bool $showDeleteModal = false;

    protected $queryString = ['search', 'statusFilter', 'paymentFilter', 'shippingFilter', 'sortField', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function openDetail(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
        $this->showDetailModal = true;
        $this->dispatch('openDetail', orderId: $orderId);
    }

    public function openStatus(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
        $this->showStatusModal = true;
        $this->dispatch('openStatus', orderId: $orderId);
    }

    public function openShipping(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
        $this->showShippingModal = true;
        $this->dispatch('openShipping', orderId: $orderId);
    }

    public function openPayment(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
        $this->showPaymentModal = true;
        $this->dispatch('openPayment', orderId: $orderId);
    }

    public function confirmDelete(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
        $this->showDeleteModal = true;
    }

    #[On('closeModal')]
    public function closeAllModals(): void
    {
        $this->showDetailModal = false;
        $this->showStatusModal = false;
        $this->showShippingModal = false;
        $this->showPaymentModal = false;
        $this->showDeleteModal = false;
        $this->selectedOrderId = null;
    }

    #[On('orderUpdated')]
    public function refreshOrders(): void
    {
        $this->resetPage();
    }

    public function deleteOrder(): void
    {
        if ($this->selectedOrderId) {
            $order = Order::find($this->selectedOrderId);
            if ($order) {
                $order->delete();
                $this->dispatch('orderUpdated');
                $this->dispatch('notify', type: 'success', message: 'Pesanan berhasil dihapus.');
            }
            $this->closeAllModals();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'paymentFilter', 'shippingFilter', 'sortField', 'sortDirection']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::with(['product', 'user']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_code', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Payment filter
        if ($this->paymentFilter) {
            $query->where('payment_status', $this->paymentFilter);
        }

        // Shipping filter
        if ($this->shippingFilter) {
            $query->where('shipping_method', $this->shippingFilter);
        }

        // Sort
        $query->orderBy($this->sortField, $this->sortDirection);

        $orders = $query->paginate(10);

        $statuses = OrderStatus::cases();
        $paymentStatuses = PaymentStatus::cases();
        $shippingMethods = ShippingMethod::cases();

        return view('livewire.admin.order.order-table', [
            'orders' => $orders,
            'statuses' => $statuses,
            'paymentStatuses' => $paymentStatuses,
            'shippingMethods' => $shippingMethods,
        ]);
    }
}
