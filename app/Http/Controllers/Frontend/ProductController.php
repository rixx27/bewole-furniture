<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display the product catalog page.
     */
    public function index()
    {
        return view('frontend.pages.products');
    }

    /**
     * Display a product's detail page.
     */
    public function show(Product $product)
    {
        // Only show active or pre-order products
        if ($product->status === 'sold_out') {
            abort(404, 'Produk tidak tersedia.');
        }

        return view('frontend.pages.product-detail', compact('product'));
    }
}
