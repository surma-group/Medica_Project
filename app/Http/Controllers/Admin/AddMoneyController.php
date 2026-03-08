<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddMoney;
use Illuminate\Http\Request;
use App\Services\AccountingService;

class AddMoneyController extends Controller
{
    /**
     * Display a listing of added money.
     */
    public function index()
    {
        // Fetch all money records with creator relationship, paginated
        $moneyRecords = AddMoney::with('creator')->latest()->paginate(10);

        return view('admin.add_money.index', compact('moneyRecords'));
    }

    /**
     * Show the form for adding money.
     */
    public function create()
    {
        return view('admin.add_money.form'); // form view
    }

    /**
     * Store a newly added money entry.
     */
    public function store(Request $request, AccountingService $accountingService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        // Example ledger IDs
        $cashLedgerId = L_CASH_DESK;        // Cash Desk
        $capitalLedgerId = L_OWNER_CAPITAL;     // Owner Capital

        // 1️⃣ Accounting transaction
        $voucher = $accountingService->addTransaction(
            amount: $request->amount,
            debitLedgerId: $cashLedgerId,
            creditLedgerId: $capitalLedgerId,
            voucherType: VT_CASH_IN,          // 1 = Add Money / Cash In
            referenceId: null,       // or AddMoney ID later
            note: $request->note
        );

        // 2️⃣ Save AddMoney record
        $addMoney = AddMoney::create([
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        // (Optional) link reference_id later
        $voucher->update([
            'reference_id' => $addMoney->id
        ]);

        return redirect()->route('add_money.index')
            ->with('success', 'Money added successfully.');
    }
}
