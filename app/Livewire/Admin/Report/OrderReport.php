<?php

namespace App\Livewire\Admin\Report;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exports\OrderReportExport;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.admin')]
#[Title('Laporan — Admin')]
class OrderReport extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $startDate = '';

    #[Url(history: true)]
    public string $endDate = '';

    #[Url(history: true)]
    public string $status = '';

    #[Url(history: true)]
    public string $search = '';

    public int $perPage = 15;

    public function updatedStartDate(): void
    {
        $this->validateDateRange();
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->validateDateRange();
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Validate date range to ensure end date is not earlier than start date.
     */
    protected function validateDateRange(): bool
    {
        $this->resetErrorBag(['startDate', 'endDate', 'dateRange']);

        if (!empty($this->startDate) && !empty($this->endDate)) {
            if ($this->endDate < $this->startDate) {
                $this->addError('dateRange', 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.');
                return false;
            }
        }

        return true;
    }

    /**
     * Single source of truth for filtered orders query.
     * Used across Table, Summary calculations, and Export.
     */
    public function getFilteredOrdersQuery(): Builder
    {
        $query = Order::query()->with(['product', 'user']);

        // Date range filtering
        if (!empty($this->startDate) && !empty($this->endDate)) {
            if ($this->endDate >= $this->startDate) {
                $query->whereDate('created_at', '>=', $this->startDate)
                      ->whereDate('created_at', '<=', $this->endDate);
            }
        } elseif (!empty($this->startDate)) {
            $query->whereDate('created_at', '>=', $this->startDate);
        } elseif (!empty($this->endDate)) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        // Status filtering
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Search query (Order Code, Customer Name, WhatsApp Number)
        if (!empty($this->search)) {
            $search = trim($this->search);
            $query->where(function (Builder $q) use ($search) {
                $q->where('order_code', 'like', '%' . $search . '%')
                  ->orWhere('customer_name', 'like', '%' . $search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $search . '%')
                  ->orWhere('whatsapp_number', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Reset all active filters to default state.
     */
    public function resetFilters(): void
    {
        $this->reset(['startDate', 'endDate', 'status', 'search']);
        $this->resetErrorBag();
        $this->resetPage();
    }

    /**
     * Export currently filtered data to Excel (.xlsx).
     */
    public function exportExcel(): ?BinaryFileResponse
    {
        if (!$this->validateDateRange()) {
            return null;
        }

        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'status' => $this->status,
            'search' => $this->search,
        ];

        $fileName = 'laporan-bewole-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new OrderReportExport($filters), $fileName);
    }

    public function render()
    {
        $this->validateDateRange();

        $baseQuery = $this->getFilteredOrdersQuery();

        // Calculate summary cards matching active filters
        $totalOrders = (clone $baseQuery)->count();

        // Revenue: paid orders or completed orders, strictly excluding cancelled orders
        $totalRevenue = (clone $baseQuery)
            ->where(function ($q) {
                $q->where('payment_status', PaymentStatus::Paid->value)
                  ->orWhere('status', OrderStatus::Completed->value);
            })
            ->where('status', '!=', OrderStatus::Cancelled->value)
            ->sum('total_price');

        $completedOrders = (clone $baseQuery)
            ->where('status', OrderStatus::Completed->value)
            ->count();

        $cancelledOrders = (clone $baseQuery)
            ->where('status', OrderStatus::Cancelled->value)
            ->count();

        $orders = (clone $baseQuery)->paginate($this->perPage);
        $statuses = OrderStatus::cases();

        return view('livewire.admin.report.order-report', [
            'orders' => $orders,
            'statuses' => $statuses,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
        ]);
    }
}
