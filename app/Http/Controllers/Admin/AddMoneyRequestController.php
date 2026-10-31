<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddMoneyRequest;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AddMoneyRequestController extends Controller
{
    /**
     * LIST (Pending / All Requests)
     */
    public function index(Request $request)
    {
        $query = AddMoneyRequest::with(['user', 'paymentAccount'])
            ->latest();

        // optional filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20);

        return view('admin.add_money_requests.index', compact('requests'));
    }

    /**
     * APPROVE REQUEST
     */
    public function approve(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:add_money_requests,id',
            'approve_note' => 'nullable|string',
        ]);

        $accountingService = app(\App\Services\AccountingService::class);

        DB::beginTransaction();

        try {

            $addMoney = AddMoneyRequest::where('id', $request->id)
                ->where('status', 0)
                ->lockForUpdate()
                ->firstOrFail();

            // =========================
            // GET PARTNER + LEDGER
            // =========================
            $partner = Partner::with('ledger')
                ->where('user_id', $addMoney->user_id)
                ->firstOrFail();

            // =========================
            // LEDGERS
            // =========================
            $companyCashLedgerId = L_CASH_DESK; // Company Cash/Bank Ledger
            $partnerLedgerId = $partner->ledger_id;

            // =========================
            // 1️⃣ ACCOUNTING TRANSACTION (DOUBLE ENTRY)
            // Cash ↓ | Partner ↑
            // =========================
            $accountingService->addTransaction(
                amount: $addMoney->amount,
                debitLedgerId: $companyCashLedgerId,   // company cash decreases
                creditLedgerId: $partnerLedgerId,      // partner increases
                voucherType: VT_ADD_MONEY_APPROVE,
                referenceId: $addMoney->id,
                note: $request->approve_note ?? 'Add Money Approved (TXN: ' . $addMoney->transaction_id . ')',
            );

            // =========================
            // 2️⃣ UPDATE REQUEST
            // =========================
            $addMoney->update([
                'status' => 1,
                'approve_note' => $request->approve_note,
                'approve_by' => auth()->id(),
                'approve_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Add money request approved successfully'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * REJECT REQUEST
     */
    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:add_money_requests,id',
            'approve_note' => 'nullable|string',
        ]);

        try {

            $addMoney = AddMoneyRequest::where('id', $request->id)
                ->where('status', 0)
                ->firstOrFail();

            $addMoney->update([
                'status' => 2,
                'approve_note' => $request->approve_note,
                'approve_by' => Auth::id(),
                'approve_at' => now(),
            ]);

            return response()->json([
                'message' => 'Request rejected successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
