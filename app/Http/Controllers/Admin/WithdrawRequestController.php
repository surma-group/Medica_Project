<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use App\Models\Ledger;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WithdrawRequestController extends Controller
{
    /**
     * Display all withdrawal requests.
     */
    public function index()
    {
        $withdrawRequests = WithdrawRequest::with([
            'reference',
            'approver',
            'creator',
            'payer',
        ])
            ->latest('created_at')
            ->paginate(20);

        return view(
            'admin.withdraw_requests.index',
            compact('withdrawRequests')
        );
    }

    /**
     * Reject a pending withdrawal request.
     */
    public function reject(Request $request)
    {
        $validated = $request->validate([
            'request_id' => [
                'required',
                'integer',
                'exists:withdraw_requests,id',
            ],
            'note' => [
                'required',
                'string',
                'max:500',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $withdrawRequest = WithdrawRequest::whereKey(
                $validated['request_id']
            )
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $withdrawRequest->status !== 0) {
                throw ValidationException::withMessages([
                    'request_id' =>
                        'এই Withdrawal Request ইতোমধ্যে প্রসেস করা হয়েছে।',
                ]);
            }

            $withdrawRequest->update([
                'status' => 2,
                'admin_note' => $validated['note'],
                'approve_by' => auth('admin')->id() ?? auth()->id(),
                'approve_at' => now(),
            ]);
        });

        return redirect()->back()->with(
            'success',
            'Withdrawal request rejected successfully.'
        );
    }

    /**
     * Approve a pending withdrawal request.
     */
    public function approve(
        Request $request,
        AccountingService $accountingService
    ) {
        $validated = $request->validate([
            'request_id' => [
                'required',
                'integer',
                'exists:withdraw_requests,id',
            ],
            'note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $accountingService
        ) {
            $withdrawRequest = WithdrawRequest::whereKey(
                $validated['request_id']
            )
                ->lockForUpdate()
                ->firstOrFail();

            // Prevent duplicate approval.
            if ((int) $withdrawRequest->status !== 0) {
                throw ValidationException::withMessages([
                    'request_id' =>
                        'এই Withdrawal Request ইতোমধ্যে প্রসেস করা হয়েছে।',
                ]);
            }

            /*
             * Resolve Employee/Partner from reference_type.
             *
             * This requires the numeric morphMap:
             * 5 => Employee
             * 2 => Partner (Dealer)
             * 3 => Partner (Agent)
             */
            $withdrawRequest->load('reference');

            $accountHolder = $withdrawRequest->reference;

            if (!$accountHolder) {
                throw new \RuntimeException(
                    'Withdrawal Request-এর Employee/Partner পাওয়া যায়নি।'
                );
            }

            if (empty($accountHolder->ledger_id)) {
                throw new \RuntimeException(
                    'এই Employee/Partner-এর Ledger তৈরি করা নেই।'
                );
            }

            $amount = round((float) $withdrawRequest->amount, 2);

            if ($amount <= 0) {
                throw new \RuntimeException(
                    'Withdrawal amount সঠিক নয়।'
                );
            }

            // Lock the wallet ledger.
            $accountHolderLedger = Ledger::whereKey(
                $accountHolder->ledger_id
            )
                ->lockForUpdate()
                ->first();

            if (!$accountHolderLedger) {
                throw new \RuntimeException(
                    'Employee/Partner Ledger পাওয়া যায়নি।'
                );
            }

            // Company Cash ledger.
            $companyCashLedger = Ledger::whereKey(L_CASH_DESK)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                (int) $accountHolderLedger->id ===
                (int) $companyCashLedger->id
            ) {
                throw new \RuntimeException(
                    'Wallet Ledger এবং Company Cash Ledger একই হতে পারবে না।'
                );
            }

            /*
             * Keep the accounting direction from your original code.
             * Confirm the balance formulas before live use.
             */
            $accountingService->addTransaction(
                amount: $amount,
                debitLedgerId: $accountHolderLedger->id,
                creditLedgerId: $companyCashLedger->id,
                voucherType: VT_WITHDRAW_APPROVE,
                referenceId: $withdrawRequest->id,
                note: $validated['note']
                    ?? 'Withdrawal approved - Request #'
                        . $withdrawRequest->id,
                createdBy: auth('admin')->id() ?? auth()->id()
            );

            // Mark the request approved in the same DB transaction.
            $withdrawRequest->update([
                'status' => 1,
                'admin_note' => $validated['note'] ?? null,
                'approve_by' => auth('admin')->id() ?? auth()->id(),
                'approve_at' => now(),
            ]);
        });

        return redirect()->back()->with(
            'success',
            'Withdrawal request approved and accounting transaction recorded successfully.'
        );
    }
}
