<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddMoney;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard'); // Create this view
    }
}
