<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Admin can view any order, user can only view their own
        return $user->hasRole('admin') || $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the order status.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update shipping information.
     */
    public function updateShipping(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update payment status.
     */
    public function updatePayment(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        // Admin can cancel any order, user can cancel their own pending orders
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->id === $order->user_id && $order->status === 'pending';
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can export orders.
     */
    public function export(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
