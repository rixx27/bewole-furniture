<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CompanyProfileController extends Controller
{
    /**
     * Display the company profile management page.
     */
    public function index()
    {
        return view('admin.company-profile.index');
    }
}
