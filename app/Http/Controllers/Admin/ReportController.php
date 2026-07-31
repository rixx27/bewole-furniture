<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ShippingMethod;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display the order report page.
     */
    public function orders(Request $request)
    {
        $query = Order::with('product');

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Shipping method filter
        if ($request->filled('shipping_method')) {
            $query->where('shipping_method', $request->shipping_method);
        }

        // Customer filter
        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }

        // Product filter
        if ($request->filled('product')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->product . '%');
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Calculate totals
        $totalOrders = $orders->total();
        $totalRevenue = $query->where('payment_status', 'paid')->sum('total_price');

        return view('admin.reports.orders', compact('orders', 'totalOrders', 'totalRevenue'));
    }

    /**
     * Export order report to PDF.
     */
    public function ordersPdf(Request $request)
    {
        $query = Order::with('product');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('shipping_method')) {
            $query->where('shipping_method', $request->shipping_method);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        $totalOrders = $orders->count();
        $totalRevenue = $orders->where('payment_status', 'paid')->sum('total_price');
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $adminName = auth()->user()->name;

        $pdf = Pdf::loadView('admin.reports.pdf', compact(
            'orders', 'totalOrders', 'totalRevenue', 'startDate', 'endDate', 'adminName'
        ));

        return $pdf->download('Laporan-Pesanan-' . now()->format('Ymd-His') . '.pdf');
    }
}
