<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop')
                ->with('error', 'Your cart is empty');
        }

        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // ✅ districts load for dropdown
        $districts = District::orderBy('name_en', 'asc')->get();

        return view('frontend.checkout.index', compact(
            'cart',
            'total',
            'districts'
        ));
    }
}