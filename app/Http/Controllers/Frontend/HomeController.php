<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;

class HomeController extends Controller
{
    /**
     * Display the frontend home page with the active hero banner.
     */
    public function index()
    {
        $hero = HeroBanner::active()
            ->sorted()
            ->first();

        return view('frontend.pages.home', compact('hero'));
    }
}
