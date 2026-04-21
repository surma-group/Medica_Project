<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;

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

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'address'    => 'required',
            'phone'      => 'required',
            'email'      => 'required|email',
            'city'       => 'required',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Cart is empty!');
        }

        // 1. customer create/update
        $customer = \App\Models\Customer::updateOrCreate(
            ['phone' => $request->phone],
            [
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'address' => $request->address,
                'district_id' => $request->city,
                'postcode' => $request->postcode,
            ]
        );

        // 2. calculate total
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $delivery = 0;
        $discount = 0;
        $grandTotal = $subtotal + $delivery - $discount;

        // 3. create order
        $order = \App\Models\Order::create([
            'customer_id' => $customer->id,
            'order_number' => 'ORD-' . time(),
            'subtotal' => $subtotal,
            'delivery_charge' => $delivery,
            'discount' => $discount,
            'grand_total' => $grandTotal,
            'payment_method' => $request->payment ?? 1,
            'status' => 0,
            'note' => $request->note,
        ]);

        // 4. order items
        foreach ($cart as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $item['price'] * $item['quantity'],
            ]);
        }

        // 5. clear cart
        session()->forget('cart');

        return redirect()->route('shop')->with('success', 'Order placed successfully!');
    }
}
