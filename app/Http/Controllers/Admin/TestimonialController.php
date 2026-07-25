<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.placeholder', [
            'title' => __('Testimonials'),
            'description' => __('Manage customer testimonials.'),
        ]);
    }
}

