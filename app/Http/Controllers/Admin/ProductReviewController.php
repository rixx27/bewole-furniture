<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ProductReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.placeholder', [
            'title' => __('Product Reviews'),
            'description' => __('Manage product reviews from customers.'),
        ]);
    }
}

