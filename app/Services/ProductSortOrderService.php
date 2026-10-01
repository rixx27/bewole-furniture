<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSortOrderService
{
    /**
     * Get the next available sort order for a category.
     */
    public function getNextSortOrder(?int $categoryId): int
    {
        if (!$categoryId) {
            return 1;
        }

        $this->ensureNormalized($categoryId);

        $count = Product::where('category_id', $categoryId)->count();
        $maxOrder = Product::where('category_id', $categoryId)->max('sort_order') ?? 0;

        return max($count + 1, $maxOrder + 1);
    }

    /**
     * Ensure all products in a category have valid, sequential sort orders (1, 2, 3...)
     */
    public function ensureNormalized(?int $categoryId): void
    {
        if (!$categoryId) {
            return;
        }

        $hasInvalid = Product::where('category_id', $categoryId)
            ->where(function ($q) {
                $q->whereNull('sort_order')
                  ->orWhere('sort_order', '<=', 0);
            })->exists();

        $hasDuplicates = Product::where('category_id', $categoryId)
            ->where('sort_order', '>', 0)
            ->groupBy('sort_order')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasInvalid || $hasDuplicates) {
            $this->normalizeCategoryOrders($categoryId);
        }
    }

    /**
     * Resequence all products in a category sequentially from 1..N.
     */
    public function normalizeCategoryOrders(int $categoryId): void
    {
        DB::transaction(function () use ($categoryId) {
            $products = Product::where('category_id', $categoryId)
                ->orderByRaw('CASE WHEN sort_order = 0 OR sort_order IS NULL THEN 1 ELSE 0 END ASC')
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $order = 1;
            foreach ($products as $p) {
                if ((int) $p->sort_order !== $order) {
                    Product::where('id', $p->id)->update(['sort_order' => $order]);
                }
                $order++;
            }
        });
    }

    /**
     * Adjust sort orders in category when a new product is being created.
     */
    public function adjustOnCreating(Product $product): void
    {
        $categoryId = $product->category_id;

        if (!$categoryId) {
            return;
        }

        $this->ensureNormalized($categoryId);

        $newOrder = (int) ($product->sort_order ?? 0);

        // If no sort order is provided (or <= 0), automatically assign the next available rank
        if ($newOrder <= 0) {
            $product->sort_order = $this->getNextSortOrder($categoryId);
            return;
        }

        // If an explicit sort order is given, shift all products with sort_order >= $newOrder up by 1
        DB::transaction(function () use ($categoryId, $newOrder) {
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

        if ($oldCategoryId) {
            $this->ensureNormalized($oldCategoryId);
        }
        if ($newCategoryId && $newCategoryId != $oldCategoryId) {
            $this->ensureNormalized($newCategoryId);
        }

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
