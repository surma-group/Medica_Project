<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderRequest;

class OrderRequestController extends Controller
{
    /**
     * Display a listing of the order requests
     */
    public function index()
    {
        $orders = OrderRequest::with('customer')
            ->latest()
            ->paginate(15);

        return view('admin.order_requests.index', compact('orders'));
    }

    /**
     * Display the specified order request
     */
    public function show($id)
    {
        $order = OrderRequest::with(['customer', 'items'])
            ->findOrFail($id);

        return view('admin.order_requests.show', compact('order'));
    }
}