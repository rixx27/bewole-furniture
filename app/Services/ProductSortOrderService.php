<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSortOrderService
{
    /**
     * Adjust sort orders in category when a new product is being created.
     */
    public function adjustOnCreating(Product $product): void
    {
        $newOrder = (int) ($product->sort_order ?? 0);
        $categoryId = $product->category_id;

        if (!$categoryId || $newOrder <= 0) {
            return;
        }

        DB::transaction(function () use ($categoryId, $newOrder) {
            // Shift all products in the same category with sort_order >= $newOrder up by 1
            Product::where('category_id', $categoryId)
                ->where('sort_order', '>=', $newOrder)
                ->increment('sort_order');
        });
    }

    /**
     * Adjust sort orders in category when an existing product is being updated.
     */
    public function adjustOnUpdating(Product $product): void
    {
        $oldOrder = (int) $product->getOriginal('sort_order');
        $newOrder = (int) ($product->sort_order ?? 0);
        $oldCategoryId = $product->getOriginal('category_id');
        $newCategoryId = $product->category_id;

        $this->adjustSortOrder(
            categoryId: $newCategoryId,
            newOrder: $newOrder,
            oldOrder: $oldOrder,
            productId: $product->id,
            oldCategoryId: $oldCategoryId
        );
    }

    /**
     * Adjust sort orders in category when a product is deleted.
     */
    public function adjustOnDeleted(Product $product): void
    {
        $categoryId = $product->category_id;
        $order = (int) $product->sort_order;

        if (!$categoryId || $order <= 0) {
            return;
        }

        DB::transaction(function () use ($categoryId, $order, $product) {
            Product::where('category_id', $categoryId)
                ->where('id', '!=', $product->id)
                ->where('sort_order', '>', $order)
                ->decrement('sort_order');
        });
    }

    /**
     * Core logic for adjusting sort order within categories.
     */
    public function adjustSortOrder(
        ?int $categoryId,
        int $newOrder,
        int $oldOrder = 0,
        ?int $productId = null,
        ?int $oldCategoryId = null
    ): void {
        DB::transaction(function () use ($categoryId, $newOrder, $oldOrder, $productId, $oldCategoryId) {
            // Case 1: Category has changed
            if ($oldCategoryId && $categoryId && $oldCategoryId != $categoryId) {
                // In old category: close gap if it was ranked (> 0)
                if ($oldOrder > 0) {
                    Product::where('category_id', $oldCategoryId)
                        ->when($productId, fn($q) => $q->where('id', '!=', $productId))
                        ->where('sort_order', '>', $oldOrder)
                        ->decrement('sort_order');
                }

                // In new category: make room if newOrder > 0
                if ($newOrder > 0) {
                    Product::where('category_id', $categoryId)
                        ->when($productId, fn($q) => $q->where('id', '!=', $productId))
                        ->where('sort_order', '>=', $newOrder)
                        ->increment('sort_order');
                }

                return;
            }

            // Category unchanged
            if (!$categoryId) {
                return;
            }

            // Case 2: Product moved to 0 / unranked
            if ($newOrder <= 0) {
                if ($oldOrder > 0) {
                    // Close the gap in current category
                    Product::where('category_id', $categoryId)
                        ->when($productId, fn($q) => $q->where('id', '!=', $productId))
                        ->where('sort_order', '>', $oldOrder)
                        ->decrement('sort_order');
                }
                return;
            }

            // Case 3: Product was previously unranked (<= 0), now assigned rank > 0
            if ($oldOrder <= 0) {
                Product::where('category_id', $categoryId)
                    ->when($productId, fn($q) => $q->where('id', '!=', $productId))
                    ->where('sort_order', '>=', $newOrder)
                    ->increment('sort_order');
                return;
            }

            // Case 4: Moving UP (e.g. from 2 to 1)
            // Existing products between [newOrder, oldOrder - 1] shift UP (+1)
            if ($newOrder < $oldOrder) {
                Product::where('category_id', $categoryId)
                    ->when($productId, fn($q) => $q->where('id', '!=', $productId))
                    ->where('sort_order', '>=', $newOrder)
                    ->where('sort_order', '<', $oldOrder)
                    ->increment('sort_order');
                return;
            }

            // Case 5: Moving DOWN (e.g. from 1 to 3)
            // Existing products between [oldOrder + 1, newOrder] shift DOWN (-1)
            if ($newOrder > $oldOrder) {
                Product::where('category_id', $categoryId)
                    ->when($productId, fn($q) => $q->where('id', '!=', $productId))
                    ->where('sort_order', '>', $oldOrder)
                    ->where('sort_order', '<=', $newOrder)
                    ->decrement('sort_order');
                return;
            }

            // Case 6: $newOrder == $oldOrder -> nothing to do
        });
    }
}
