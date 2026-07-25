<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.placeholder', [
            'title' => __('Product Gallery'),
            'description' => __('Manage product images and galleries.'),
        ]);
    }
}

