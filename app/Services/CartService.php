<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'bewole_cart';

    /**
     * Get all cart items from session.
     */
    public function getCart(): array
    {
        return Session::get($this->sessionKey, []);
    }

    /**
     * Add a product to the cart.
     */
    public function add(int $productId, int $quantity = 1): array
    {
        $product = Product::active()->find($productId);

        if (!$product) {
            return $this->getCart();
        }

        $cart = $this->getCart();
        $qty = max(1, $quantity);

        if (isset($cart[$productId])) {
            $newQty = $cart[$productId]['quantity'] + $qty;
            if ($product->stock > 0 && $newQty > $product->stock) {
                $newQty = $product->stock;
            }
            $cart[$productId]['quantity'] = $newQty;
        } else {
            if ($product->stock > 0 && $qty > $product->stock) {
                $qty = $product->stock;
            }

            $cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (int) ($product->discount_price ?? $product->price),
                'original_price' => (int) $product->price,
                'formatted_price' => $product->formatted_discount_price ?: $product->formatted_price,
                'quantity' => $qty,
                'thumbnail' => $product->thumbnail,
                'category_name' => $product->category?->name ?? 'Furniture',
                'stock' => $product->stock,
            ];
        }

        Session::put($this->sessionKey, $cart);

        return $cart;
    }

    /**
     * Update quantity of a product in cart.
     */
    public function updateQuantity(int $productId, int $quantity): array
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $product = Product::find($productId);
                if ($product && $product->stock > 0 && $quantity > $product->stock) {
                    $quantity = $product->stock;
                }
                $cart[$productId]['quantity'] = $quantity;
            }
            Session::put($this->sessionKey, $cart);
        }

        return $cart;
    }

    /**
     * Remove item from cart.
     */
    public function remove(int $productId): array
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put($this->sessionKey, $cart);
        }

        return $cart;
    }

    /**
     * Clear all items from cart.
     */
    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }

    /**
     * Calculate subtotal of cart items.
     */
    public function getSubtotal(): int
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += ($item['price'] * $item['quantity']);
        }

        return $total;
    }

    /**
     * Get total quantity of items in cart.
     */
    public function getItemCount(): int
    {
        $cart = $this->getCart();
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }
}
