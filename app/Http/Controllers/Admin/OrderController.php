<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Requests\Admin\UpdateShippingRequest;
use App\Http\Requests\Admin\UpdatePaymentRequest;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        Gate::authorize('viewAny', Order::class);

        return view('admin.orders.index');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        Gate::authorize('view', $order);

        $order->load([
            'user',
            'product',
            'product.images',
            'statusHistories',
            'statusHistories.changedBy',
            'review',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Store a newly created order (manual order by admin).
     */
    public function store(StoreOrderRequest $request)
    {
        Gate::authorize('create', Order::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $product = \App\Models\Product::findOrFail($data['product_id']);
        $unitPrice = (int) ($product->discount_price ?? $product->price);
        $quantity = (int) ($data['quantity'] ?? 1);
        $data['total_price'] = $unitPrice * $quantity;
        $data['customization_fee'] = 0;
        $data['packing_fee'] = 0;

        $order = $this->orderService->createOrder($data, [
            $product->id => [
                'quantity' => $quantity,
            ],
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Pesanan "' . $order->order_code . '" berhasil dibuat.');
    }

    /**
     * Update order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        Gate::authorize('updateStatus', $order);

        $status = OrderStatus::tryFrom($request->input('status'));

        if (!$status) {
            return back()->with('error', 'Status tidak valid.');
        }

        try {
            $this->orderService->updateStatus($order, $status, $request->input('notes'));
            return back()->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update shipping information.
     */
    public function updateShipping(UpdateShippingRequest $request, Order $order)
    {
        Gate::authorize('updateShipping', $order);

        try {
            $this->orderService->updateShipping($order, $request->validated());
            return back()->with('success', 'Informasi pengiriman berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update payment status.
     */
    public function updatePayment(UpdatePaymentRequest $request, Order $order)
    {
        Gate::authorize('updatePayment', $order);

        $paymentStatus = PaymentStatus::tryFrom($request->input('payment_status'));

        if (!$paymentStatus) {
            return back()->with('error', 'Status pembayaran tidak valid.');
        }

        try {
            $this->orderService->updatePayment($order, $paymentStatus, $request->input('notes'));
            return back()->with('success', 'Status pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel order.
     */
    public function cancel(Request $request, Order $order)
    {
        Gate::authorize('cancel', $order);

        try {
            $this->orderService->updateStatus(
                $order,
                OrderStatus::Cancelled,
                $request->input('notes', 'Dibatalkan oleh ' . $request->user()->name)
            );

            return back()->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        Gate::authorize('delete', $order);

        $code = $order->order_code;
        $order->statusHistories()->delete();
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Pesanan "' . $code . '" berhasil dihapus.');
    }

    /**
     * Generate invoice PDF for the order.
     */
    public function invoice(Order $order)
    {
        Gate::authorize('view', $order);

        $order->load(['items.product', 'product']);

        $pdf = Pdf::loadView('admin.invoices.order', compact('order'));

        return $pdf->download('INVOICE-' . $order->order_code . '.pdf');
    }
}

