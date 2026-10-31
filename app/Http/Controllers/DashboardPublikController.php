<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardPublikController extends Controller
{
    public function index()
    {
        return view('dashboard-publik');
    }
}
