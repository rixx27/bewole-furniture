<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    /**
     * Display the website settings management page.
     */
    public function index()
    {
        return view('admin.settings.index');
    }
}

