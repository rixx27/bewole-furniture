<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingMethod;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    protected OrderCodeGenerator $orderCodeGenerator;

    public function __construct(OrderCodeGenerator $orderCodeGenerator)
    {
        $this->orderCodeGenerator = $orderCodeGenerator;
    }

    /**
     * Create a new order from checkout data.
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $data['order_code'] = $this->orderCodeGenerator->generate();
            $data['status'] = OrderStatus::Pending->value;
            $data['payment_status'] = PaymentStatus::Unpaid->value;

            $order = Order::create($data);

            // Create initial status history
            $this->createStatusHistory($order, OrderStatus::Pending, 'Pesanan baru dibuat');

            return $order;
        });
    }

    /**
     * Update order status with validation.
     */
    public function updateStatus(Order $order, OrderStatus $newStatus, ?string $notes = null): Order
    {
        $currentStatus = OrderStatus::tryFrom($order->status);

        if (!$currentStatus) {
            throw new \InvalidArgumentException("Invalid current status: {$order->status}");
        }

        // Validate transition
        if (!$currentStatus->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Status tidak dapat diubah dari '{$currentStatus->label()}' ke '{$newStatus->label()}'."
            );
        }

        return DB::transaction(function () use ($order, $newStatus, $notes) {
            $oldStatus = $order->status;
            $order->update(['status' => $newStatus->value]);

            // Create status history
            $this->createStatusHistory($order, $newStatus, $notes, $oldStatus);

            return $order->fresh();
        });
    }

    /**
     * Update payment status.
     */
    public function updatePayment(Order $order, PaymentStatus $paymentStatus, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($order, $paymentStatus, $notes) {
            $order->update(['payment_status' => $paymentStatus->value]);

            // Create status history for payment change
            $this->createStatusHistory(
                $order,
                OrderStatus::tryFrom($order->status),
                $notes ?: "Pembayaran: {$paymentStatus->label()}"
            );

            return $order->fresh();
        });
    }

    /**
     * Update shipping information based on shipping method.
     */
    public function updateShipping(Order $order, array $data): Order
    {
        $shippingMethod = ShippingMethod::tryFrom($data['shipping_method']);

        if (!$shippingMethod) {
            throw new \InvalidArgumentException("Metode pengiriman tidak valid.");
        }

        return DB::transaction(function () use ($order, $shippingMethod, $data) {
            $shippingData = ['shipping_method' => $shippingMethod->value];

            switch ($shippingMethod) {
                case ShippingMethod::Expedition:
                    $shippingData = array_merge($shippingData, [
                        'courier' => $data['courier'],
                        'tracking_number' => $data['tracking_number'],
                        'shipping_date' => $data['shipping_date'],
                        'driver_name' => null,
                        'vehicle_number' => null,
                        'pickup_date' => null,
                    ]);
                    break;

                case ShippingMethod::InternalDelivery:
                    $shippingData = array_merge($shippingData, [
                        'driver_name' => $data['driver_name'],
                        'vehicle_number' => $data['vehicle_number'],
                        'shipping_date' => $data['shipping_date'],
                        'courier' => null,
                        'tracking_number' => null,
                        'pickup_date' => null,
                    ]);
                    break;

                case ShippingMethod::SelfPickup:
                    $shippingData = array_merge($shippingData, [
                        'pickup_date' => $data['pickup_date'],
                        'courier' => null,
                        'tracking_number' => null,
                        'driver_name' => null,
                        'vehicle_number' => null,
                        'shipping_date' => null,
                    ]);
                    break;
            }

            $order->update($shippingData);

            // Update status to shipped
            if ($order->status === OrderStatus::ReadyToShip->value) {
                $this->updateStatus($order, OrderStatus::Shipped, "Pengiriman via {$shippingMethod->label()}");
            }

            return $order->fresh();
        });
    }

    /**
     * Create a status history record.
     */
    private function createStatusHistory(
        Order $order,
        OrderStatus $status,
        ?string $notes = null,
        ?string $previousStatus = null
    ): OrderStatusHistory {
        $description = $notes;

        if (!$description) {
            $description = "Status berubah menjadi {$status->label()}";
            if ($previousStatus) {
                $previousLabel = OrderStatus::tryFrom($previousStatus)?->label() ?? $previousStatus;
                $description = "Status berubah dari {$previousLabel} menjadi {$status->label()}";
            }
        }

        return OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $status->value,
            'description' => $description,
            'changed_by' => Auth::id(),
        ]);
    }

    /**
     * Get dashboard statistics.
     */
    public function getDashboardStats(): array
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', OrderStatus::Completed->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->sum('total_price');
        $pendingOrders = Order::where('status', OrderStatus::Pending->value)->count();
        $processingOrders = Order::whereIn('status', [
            OrderStatus::Processing->value,
            OrderStatus::ReadyToShip->value,
        ])->count();
        $shippedOrders = Order::where('status', OrderStatus::Shipped->value)->count();
        $completedOrders = Order::where('status', OrderStatus::Completed->value)->count();
        $totalProductsSold = Order::where('status', OrderStatus::Completed->value)->sum('quantity');

        $monthlySales = Order::selectRaw('MONTH(created_at) as month, SUM(total_price) as total, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->where('status', OrderStatus::Completed->value)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Prepare chart data for all 12 months
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = now()->month($i)->format('M');
            $data = $monthlySales->get($i);
            $chartData[] = [
                'month' => $monthName,
                'total' => (int) ($data->total ?? 0),
                'count' => (int) ($data->count ?? 0),
            ];
        }

        return [
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'pendingOrders' => $pendingOrders,
            'processingOrders' => $processingOrders,
            'shippedOrders' => $shippedOrders,
            'completedOrders' => $completedOrders,
            'totalProductsSold' => $totalProductsSold,
            'chartData' => $chartData,
            'recentOrders' => Order::with('product')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
}
