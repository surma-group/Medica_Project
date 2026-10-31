<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('frontend.profile.index');
    }

    public function edit()
    {
        return view('frontend.profile.edit');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully');
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated');
    }

    public function orders()
    {
        $user = auth()->user();

        // user -> customer
        $customer = \App\Models\Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            $orders = collect();
        } else {
            $orders = \App\Models\Order::where('customer_id', $customer->id)
                ->latest()
                ->get();
        }

        return view('frontend.profile.orders', compact('orders'));
    }

    public function showOrder($id)
    {
        $user = auth()->user();

        $customer = \App\Models\Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            abort(404);
        }

        $order = \App\Models\Order::where('customer_id', $customer->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('frontend.profile.order-show', compact('order'));
    }
}
