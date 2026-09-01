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
     * Create a new order from checkout data and cart items.
     */
    public function createOrder(array $data, array $cartItems = []): Order
    {
        return DB::transaction(function () use ($data, $cartItems) {
            $order = new Order();
            $order->user_id = $data['user_id'] ?? Auth::id();
            $order->product_id = $data['product_id'] ?? null;
            $order->order_code = $this->orderCodeGenerator->generate();
            $order->customer_name = $data['customer_name'] ?? '';
            $order->customer_phone = $data['customer_phone'] ?? '';
            $order->customer_email = $data['customer_email'] ?? null;
            $order->shipping_address = $data['shipping_address'] ?? '';
            $order->city = $data['city'] ?? '';
            $order->postal_code = $data['postal_code'] ?? null;
            $order->meubel_type = $data['meubel_type'] ?? null;
            $order->packing_type = $data['packing_type'] ?? null;
            $order->customization_details = $data['customization_details'] ?? null;
            $order->customization_fee = $data['customization_fee'] ?? 0;
            $order->packing_fee = $data['packing_fee'] ?? 0;
            $order->quantity = (int) ($data['quantity'] ?? 1);
            $order->total_price = $data['total_price'] ?? 0;
            $order->notes = $data['notes'] ?? null;
            $order->status = OrderStatus::Pending->value;
            $order->payment_status = PaymentStatus::Unpaid->value;
            $order->payment_method = $data['payment_method'] ?? 'manual_transfer';
            $order->whatsapp_number = $data['whatsapp_number'] ?? ($data['customer_phone'] ?? null);
            $order->save();

            if (!empty($cartItems)) {
                foreach ($cartItems as $productId => $item) {
                    $product = \App\Models\Product::find($productId);
                    if (!$product) {
                        continue;
                    }

                    $qty = (int) ($item['quantity'] ?? 1);
                    $unitPrice = (int) ($product->discount_price ?? $product->price);
                    $meubelType = $data['meubel_type'] ?? null;
                    $custDetails = $data['customization_details'] ?? [];
                    $custOpt = ($meubelType === 'matang' && isset($custDetails[$productId]))
                        ? $custDetails[$productId]
                        : null;

                    $itemBreakdown = $data['item_breakdowns'][$productId] ?? [];
                    $seatCost = $itemBreakdown['seat_material_cost'] ?? 0;
                    $packingCost = $itemBreakdown['packing_material_cost'] ?? 0;

                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'meubel_type' => $meubelType,
                        'customization_option' => $itemBreakdown['seat_material_name'] ?? $custOpt,
                        'packing_type' => $data['packing_type'] ?? null,
                        'seat_material_name' => $itemBreakdown['seat_material_name'] ?? null,
                        'seat_price_per_meter' => $itemBreakdown['seat_price_per_meter'] ?? 0,
                        'seat_usage_meter' => $itemBreakdown['seat_usage_meter'] ?? 0,
                        'seat_material_cost' => $seatCost,
                        'packing_material_name' => $itemBreakdown['packing_material_name'] ?? null,
                        'packing_price_per_meter' => $itemBreakdown['packing_price_per_meter'] ?? 0,
                        'packing_usage_meter' => $itemBreakdown['packing_usage_meter'] ?? 0,
                        'packing_material_cost' => $packingCost,
                        'customization_price' => $seatCost,
                        'packing_price' => $packingCost,
                        'total_price' => ($unitPrice * $qty) + $seatCost + $packingCost,
                    ]);
                }
            }

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
        if ($currentStatus && !$currentStatus->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException('Status pesanan hanya dapat dilanjutkan ke tahap berikutnya atau dibatalkan.');
        }

        return DB::transaction(function () use ($order, $newStatus, $notes) {
            $oldStatus = $order->status;
            $order->status = $newStatus->value;
            $order->save();

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
            $order->payment_status = $paymentStatus->value;
            $order->save();

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
            OrderStatus::InProduction->value,
            OrderStatus::QualityControl->value,
            OrderStatus::ReadyToShip->value,
        ])->count();
        $shippedOrders = Order::where('status', OrderStatus::Shipped->value)->count();
        $completedOrders = Order::where('status', OrderStatus::Completed->value)->count();
        $totalProductsSold = Order::where('status', OrderStatus::Completed->value)->sum('quantity');

        $monthExpr = DB::getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : 'MONTH(created_at)';

        $monthlySales = Order::selectRaw("{$monthExpr} as month, SUM(total_price) as total, COUNT(*) as count")
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
