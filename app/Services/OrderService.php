<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingMethod;
use App\Models\Order;
use App\Models\OrderPayment;
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
                    $custOpt = (in_array($meubelType, ['matang', 'finished']) && isset($custDetails[$productId]))
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
    public function updateStatus(
        Order $order,
        OrderStatus $newStatus,
        ?string $notes = null,
        ?string $photo = null,
        ?float $latitude = null,
        ?float $longitude = null
    ): Order {
        $currentStatus = OrderStatus::tryFrom($order->status);
        if ($currentStatus && !$currentStatus->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException('Status pesanan hanya dapat dilanjutkan ke tahap berikutnya atau dibatalkan.');
        }

        return DB::transaction(function () use ($order, $newStatus, $notes, $photo, $latitude, $longitude) {
            $oldStatus = $order->status;
            $order->status = $newStatus->value;
            $order->save();

            // Create status history
            $this->createStatusHistory($order, $newStatus, $notes, $oldStatus, $photo, $latitude, $longitude);

            return $order->fresh();
        });
    }

    /**
     * Update payment status and details.
     */
    public function updatePayment(
        Order $order,
        PaymentStatus $paymentStatus,
        ?string $notes = null,
        ?float $downPaymentAmount = null,
        ?string $rejectionReason = null
    ): Order {
        return DB::transaction(function () use ($order, $paymentStatus, $notes, $downPaymentAmount, $rejectionReason) {
            $order->payment_status = $paymentStatus->value;

            if ($paymentStatus === PaymentStatus::DownPayment) {
                if ($downPaymentAmount !== null && $downPaymentAmount > 0) {
                    $order->down_payment_amount = $downPaymentAmount;
                }
                $order->payment_rejection_reason = null;

                // Synchronize with order_payments: verify pending payment or create verified record
                $pendingPayment = $order->payments()->where('status', 'pending')->first();
                if ($pendingPayment) {
                    $pendingPayment->update([
                        'amount' => $downPaymentAmount ?? ($pendingPayment->amount > 0 ? $pendingPayment->amount : (float) $order->down_payment_amount),
                        'status' => 'verified',
                        'admin_notes' => $notes,
                        'verified_by' => Auth::id(),
                        'verified_at' => now(),
                    ]);
                } elseif ($order->payments()->doesntExist()) {
                    $order->payments()->create([
                        'payment_number' => 1,
                        'title' => 'Pembayaran Ke-1 (DP Awal)',
                        'amount' => (float) $order->down_payment_amount,
                        'proof_file' => $order->payment_proof,
                        'payment_method' => $order->payment_method ?: 'bank_transfer',
                        'status' => 'verified',
                        'admin_notes' => $notes,
                        'verified_by' => Auth::id(),
                        'verified_at' => now(),
                    ]);
                }
            } elseif ($paymentStatus === PaymentStatus::Paid) {
                $order->down_payment_amount = $order->total_price;
                $order->payment_rejection_reason = null;

                // Synchronize with order_payments: verify any pending payment
                $pendingPayment = $order->payments()->where('status', 'pending')->first();
                if ($pendingPayment) {
                    $rem = max(0, (float) $order->total_price - (float) $order->payments()->where('status', 'verified')->sum('amount'));
                    $pendingPayment->update([
                        'amount' => $rem > 0 ? $rem : (float) $order->total_price,
                        'status' => 'verified',
                        'admin_notes' => $notes,
                        'verified_by' => Auth::id(),
                        'verified_at' => now(),
                    ]);
                } elseif ($order->payments()->doesntExist()) {
                    $order->payments()->create([
                        'payment_number' => 1,
                        'title' => 'Pembayaran Ke-1 (Lunas)',
                        'amount' => (float) $order->total_price,
                        'proof_file' => $order->payment_proof ?: $order->final_payment_proof,
                        'payment_method' => $order->payment_method ?: 'bank_transfer',
                        'status' => 'verified',
                        'admin_notes' => $notes,
                        'verified_by' => Auth::id(),
                        'verified_at' => now(),
                    ]);
                }
            } elseif ($paymentStatus === PaymentStatus::Failed) {
                if ($rejectionReason !== null) {
                    $order->payment_rejection_reason = $rejectionReason;
                }
                // Reject pending payments
                $order->payments()->where('status', 'pending')->update([
                    'status' => 'rejected',
                    'rejection_reason' => $rejectionReason,
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                ]);
            } elseif ($paymentStatus === PaymentStatus::Unpaid) {
                $order->down_payment_amount = 0;
            }

            $order->save();

            // Build informative history note
            $historyNote = $notes;
            if (!$historyNote) {
                if ($paymentStatus === PaymentStatus::DownPayment) {
                    $dpFmt = 'Rp ' . number_format((float) ($order->down_payment_amount ?? 0), 0, ',', '.');
                    $remFmt = 'Rp ' . number_format($order->remaining_payment, 0, ',', '.');
                    $historyNote = "Pembayaran DP Diterima: {$dpFmt} (Sisa Tagihan: {$remFmt})";
                } elseif ($paymentStatus === PaymentStatus::Paid) {
                    $historyNote = "Pembayaran Lunas Diverifikasi";
                } elseif ($paymentStatus === PaymentStatus::Failed && $rejectionReason) {
                    $historyNote = "Bukti Pembayaran Ditolak: {$rejectionReason}";
                } else {
                    $historyNote = "Status Pembayaran: {$paymentStatus->label()}";
                }
            }

            // Create status history for payment change
            $this->createStatusHistory(
                $order,
                OrderStatus::tryFrom($order->status),
                $historyNote
            );

            return $order->fresh(['payments']);
        });
    }

    /**
     * Attach a payment proof image to an order as an installment.
     */
    public function attachPaymentProof(Order $order, string $filePath, bool $isFinal = false, ?string $customerNotes = null): Order
    {
        return DB::transaction(function () use ($order, $filePath, $isFinal, $customerNotes) {
            $nextNumber = (int) ($order->payments()->max('payment_number') ?? 0) + 1;

            if ($nextNumber === 1) {
                $title = 'Pembayaran Ke-1 (DP Awal)';
            } elseif ($isFinal || $order->remaining_payment <= 0) {
                $title = "Pembayaran Ke-{$nextNumber} (Pelunasan)";
            } else {
                $title = "Pembayaran Ke-{$nextNumber} (Termin Bertahap)";
            }

            // Create payment installment record
            $order->payments()->create([
                'payment_number' => $nextNumber,
                'title' => $title,
                'amount' => 0,
                'proof_file' => $filePath,
                'payment_method' => $order->payment_method ?: 'bank_transfer',
                'status' => 'pending',
                'customer_notes' => $customerNotes,
            ]);

            // Keep legacy fields in sync for backward compatibility
            if ($nextNumber === 1 || empty($order->payment_proof)) {
                $order->payment_proof = $filePath;
                $order->payment_proof_uploaded_at = now();
            }
            if ($isFinal || $nextNumber > 1) {
                $order->final_payment_proof = $filePath;
                $order->final_payment_proof_uploaded_at = now();
            }

            $order->payment_rejection_reason = null;
            if ($order->payment_status === PaymentStatus::Failed->value) {
                $order->payment_status = PaymentStatus::Unpaid->value;
            }
            $order->save();

            $note = "Bukti {$title} Diunggah oleh Pelanggan";
            $this->createStatusHistory(
                $order,
                OrderStatus::tryFrom($order->status),
                $note
            );

            return $order->fresh(['payments']);
        });
    }

    /**
     * Verify a specific payment installment with the confirmed received amount.
     */
    public function verifyPaymentInstallment(
        OrderPayment $payment,
        float $amount,
        ?string $adminNotes = null,
        ?int $verifiedByUserId = null
    ): Order {
        return DB::transaction(function () use ($payment, $amount, $adminNotes, $verifiedByUserId) {
            $order = $payment->order;

            $payment->update([
                'amount' => $amount,
                'status' => 'verified',
                'admin_notes' => $adminNotes,
                'rejection_reason' => null,
                'verified_by' => $verifiedByUserId ?: Auth::id(),
                'verified_at' => now(),
            ]);

            // Recalculate total verified payments
            $totalVerified = (float) $order->payments()->where('status', 'verified')->sum('amount');
            $order->down_payment_amount = $totalVerified;
            $order->payment_rejection_reason = null;

            if ($totalVerified >= (float) $order->total_price) {
                $order->payment_status = PaymentStatus::Paid->value;
                $historyNote = "Pembayaran #{$payment->payment_number} Diverifikasi: Rp " . number_format($amount, 0, ',', '.') . " — Tagihan Pesanan LUNAS";
            } else {
                $order->payment_status = PaymentStatus::DownPayment->value;
                $remFmt = 'Rp ' . number_format($order->remaining_payment, 0, ',', '.');
                $historyNote = "Pembayaran #{$payment->payment_number} Diverifikasi: Rp " . number_format($amount, 0, ',', '.') . " (Total Masuk: Rp " . number_format($totalVerified, 0, ',', '.') . ", Sisa: {$remFmt})";
            }

            $order->save();

            $this->createStatusHistory(
                $order,
                OrderStatus::tryFrom($order->status),
                $historyNote
            );

            return $order->fresh(['payments']);
        });
    }

    /**
     * Reject a specific payment installment with a reason.
     */
    public function rejectPaymentInstallment(
        OrderPayment $payment,
        string $reason,
        ?int $verifiedByUserId = null
    ): Order {
        return DB::transaction(function () use ($payment, $reason, $verifiedByUserId) {
            $order = $payment->order;

            $payment->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'verified_by' => $verifiedByUserId ?: Auth::id(),
                'verified_at' => now(),
            ]);

            $order->payment_rejection_reason = $reason;

            // If no verified payments exist on the order, set order status to failed
            if ($order->payments()->where('status', 'verified')->doesntExist()) {
                $order->payment_status = PaymentStatus::Failed->value;
            }

            $order->save();

            $this->createStatusHistory(
                $order,
                OrderStatus::tryFrom($order->status),
                "Bukti {$payment->title} Ditolak: {$reason}"
            );

            return $order->fresh(['payments']);
        });
    }

    /**
     * Add a manual payment installment (e.g. Cash / WA direct transfer) by Admin.
     */
    public function addManualPayment(
        Order $order,
        float $amount,
        string $paymentMethod = 'cash',
        ?string $adminNotes = null,
        ?string $proofPath = null,
        ?int $verifiedByUserId = null
    ): OrderPayment {
        return DB::transaction(function () use ($order, $amount, $paymentMethod, $adminNotes, $proofPath, $verifiedByUserId) {
            $nextNumber = (int) ($order->payments()->max('payment_number') ?? 0) + 1;

            $methodLabel = match ($paymentMethod) {
                'cash', 'cod' => 'Tunai / Cash',
                'bank_transfer', 'transfer' => 'Transfer Langsung',
                'qris' => 'QRIS',
                default => ucwords(str_replace('_', ' ', $paymentMethod)),
            };

            $title = "Pembayaran Ke-{$nextNumber} ({$methodLabel})";

            $payment = $order->payments()->create([
                'payment_number' => $nextNumber,
                'title' => $title,
                'amount' => $amount,
                'proof_file' => $proofPath,
                'payment_method' => $paymentMethod,
                'status' => 'verified',
                'admin_notes' => $adminNotes,
                'verified_by' => $verifiedByUserId ?: Auth::id(),
                'verified_at' => now(),
            ]);

            // Recalculate total verified payments
            $totalVerified = (float) $order->payments()->where('status', 'verified')->sum('amount');
            $order->down_payment_amount = $totalVerified;
            $order->payment_rejection_reason = null;

            if ($totalVerified >= (float) $order->total_price) {
                $order->payment_status = PaymentStatus::Paid->value;
                $historyNote = "Pembayaran Manual Ke-{$nextNumber} ({$methodLabel}): Rp " . number_format($amount, 0, ',', '.') . " — Tagihan Pesanan LUNAS";
            } else {
                $order->payment_status = PaymentStatus::DownPayment->value;
                $remFmt = 'Rp ' . number_format($order->remaining_payment, 0, ',', '.');
                $historyNote = "Pembayaran Manual Ke-{$nextNumber} ({$methodLabel}): Rp " . number_format($amount, 0, ',', '.') . " (Total Masuk: Rp " . number_format($totalVerified, 0, ',', '.') . ", Sisa: {$remFmt})";
            }

            $order->save();

            $this->createStatusHistory(
                $order,
                OrderStatus::tryFrom($order->status),
                $historyNote
            );

            return $payment;
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
        ?string $previousStatus = null,
        ?string $photo = null,
        ?float $latitude = null,
        ?float $longitude = null
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
            'photo' => $photo,
            'latitude' => $latitude,
            'longitude' => $longitude,
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
