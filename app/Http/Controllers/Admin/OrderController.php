<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.placeholder', [
            'title' => __('Orders'),
            'description' => __('Manage customer orders.'),
        ]);
    }
}

