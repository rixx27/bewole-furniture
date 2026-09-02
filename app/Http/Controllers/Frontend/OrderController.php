<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the authenticated user's orders.
     */
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product', 'product'])
            ->latest()
            ->paginate(10);

        return view('frontend.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Ensure the user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to view this order.');
        }

        $order->load([
            'items.product',
            'product',
            'statusHistories',
            'review.images',
        ]);

        return view('frontend.orders.show', compact('order'));
    }
}

