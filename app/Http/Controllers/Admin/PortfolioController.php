<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.placeholder', [
            'title' => __('Portfolio'),
            'description' => __('Manage portfolio projects.'),
        ]);
    }
}

