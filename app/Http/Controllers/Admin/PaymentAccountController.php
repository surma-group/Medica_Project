<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;

class PaymentAccountController extends Controller
{
    /**
     * Display a listing of payment accounts.
     */
    public function index()
    {
        $accounts = PaymentAccount::latest()->paginate(10);

        return view('admin.payment_accounts.index', compact('accounts'));
    }

    /**
     * Show form for creating new account.
     */
    public function create()
    {
        return view('admin.payment_accounts.form');
    }

    /**
     * Store new account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:150',
            'type'   => 'required|in:1,2,3',
            'number' => 'required|string|max:50',
            'status' => 'required|in:0,1',
        ]);

        PaymentAccount::create($request->all());

        return redirect()->route('payment-accounts.index')
            ->with('success', 'Payment account created successfully.');
    }

    /**
     * Edit form.
     */
    public function edit(PaymentAccount $paymentAccount)
    {
        return view('admin.payment_accounts.form', compact('paymentAccount'));
    }

    /**
     * Update account.
     */
    public function update(Request $request, PaymentAccount $paymentAccount)
    {
        $request->validate([
            'name'   => 'required|string|max:150',
            'type'   => 'required|in:1,2,3',
            'number' => 'required|string|max:50',
            'status' => 'required|in:0,1',
        ]);

        $paymentAccount->update($request->all());

        return redirect()->route('payment-accounts.index')
            ->with('success', 'Payment account updated successfully.');
    }

    /**
     * Delete account.
     */
    public function destroy(PaymentAccount $paymentAccount)
    {
        $paymentAccount->delete();

        return redirect()->route('payment-accounts.index')
            ->with('success', 'Payment account deleted successfully.');
    }
}