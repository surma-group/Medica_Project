<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawRequestController extends Controller
{
    /**
     * Display a listing of withdraw requests
     */
    public function index()
    {
        $withdrawRequests = WithdrawRequest::with(['employee'])
            ->orderBy('created_at', 'desc')
            ->paginate(20); // admin-friendly

        return view('admin.withdraw_requests.index', compact('withdrawRequests'));
    }

    public function reject(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:withdraw_requests,id',
            'note' => 'required|string|max:500',
        ]);

        $withdrawRequest = WithdrawRequest::findOrFail($request->request_id);
        $withdrawRequest->update([
            'status' => 2, // Rejected
            'admin_note' => $request->note,
            'approve_by' => auth()->id(),
            'approve_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Withdraw request rejected successfully.');
    }

    public function approve(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:withdraw_requests,id',
            'note' => 'nullable|string|max:500',
        ]);

        $withdrawRequest = WithdrawRequest::findOrFail($request->request_id);

        $accountingService = app(\App\Services\AccountingService::class);

        DB::transaction(function () use ($withdrawRequest, $request, $accountingService) {

            $employeeLedgerId = $withdrawRequest->employee->ledger_id;
            $companyCashLedgerId = L_CASH_DESK; // your company cash/bank ledger constant

            // 1️⃣ Create accounting transaction
            $accountingService->addTransaction(
                amount: $withdrawRequest->amount,
                debitLedgerId: $employeeLedgerId,       // reduce employee ledger
                creditLedgerId: $companyCashLedgerId,  // reduce company cash/bank
                voucherType: VT_WITHDRAW_APPROVE,      // define a new voucher type for withdraw
                referenceId: $withdrawRequest->employee_id,
                note: $request->note ?? "Withdraw approved for " . $withdrawRequest->employee->full_name,
            );

            // 2️⃣ Update withdraw request
            $withdrawRequest->update([
                'status' => 1, // Approved
                'admin_note' => $request->note,
                'approve_by' => auth()->id(),
                'approve_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Withdraw request approved and transaction recorded successfully.');
    }
}
