<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Gate;

class ProductReviewController extends Controller
{
    /**
     * Display a listing of the product reviews.
     */
    public function index()
    {
        Gate::authorize('viewAny', ProductReview::class);

        return view('admin.reviews.index');
    }

    /**
     * Display the specified product review.
     */
    public function show(ProductReview $productReview)
    {
        Gate::authorize('view', $productReview);

        $productReview->load([
            'user',
            'product',
            'product.images',
            'order',
            'images',
        ]);

        return view('admin.reviews.show', compact('productReview'));
    }
}

