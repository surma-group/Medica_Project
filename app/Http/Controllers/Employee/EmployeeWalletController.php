<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Services\AccountingService;
use App\Models\Employee;
use App\Models\Partner;
use App\Models\WithdrawRequest;
use App\Models\Beneficiary;
use App\Models\Ledger;

class EmployeeWalletController extends Controller
{
    public function searchAccountant($accountantId)
    {
        // Lookup accountant by account_number
        $accountant = Employee::with(['user', 'branch', 'designation'])
            ->where('account_number', $accountantId)
            ->first(); // no 'role' filter

        if (!$accountant) {
            return response()->json(['success' => false], 404);
        }

        return response()->json([
            'success'     => true,
            'id'          => $accountant->id,
            'name'        => optional($accountant->user)->name ?? 'N/A',
            'branch'      => optional($accountant->branch)->branch_name ?? 'N/A',
            'designation' => optional($accountant->designation)->name ?? 'N/A',
        ]);
    }


    public function searchTransferReceiver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transfer_type' => 'required|in:employee,dealer,agent',
            'account_number' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $type = $request->input('transfer_type');
        $accountNumber = trim($request->input('account_number'));

        if ($type === 'employee') {
            $receiver = Employee::where('account_number', $accountNumber)
                ->where('status', 1)
                ->first();

            if (!$receiver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Active Employee account পাওয়া যায়নি।',
                ], 404);
            }

            $sender = Employee::where('user_id', auth()->id())->first();

            if (!$sender) {
                return response()->json([
                    'success' => false,
                    'message' => 'আপনার Employee account পাওয়া যায়নি।',
                ], 403);
            }

            if ((int) $sender->id === (int) $receiver->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'নিজের Account-এ টাকা Transfer করা যাবে না।',
                ], 422);
            }

            if (!$receiver->ledger_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Receiver account-এর Ledger সেট করা নেই।',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'receiver' => [
                    'id' => $receiver->id,
                    'name' => $receiver->full_name,
                    'account_number' => $receiver->account_number,
                    'type' => 'Employee',
                ],
            ]);
        }

        // Partner model: 1 = Dealer, 2 = Agent
        $partnerType = $type === 'dealer' ? 1 : 2;

        $receiver = Partner::where('account_number', $accountNumber)
            ->where('type', $partnerType)
            ->where('status', 1)
            ->first();

        if (!$receiver) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($type) . ' account পাওয়া যায়নি।',
            ], 404);
        }

        if (!$receiver->ledger_id) {
            return response()->json([
                'success' => false,
                'message' => 'Receiver account-এর Ledger সেট করা নেই।',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'receiver' => [
                'id' => $receiver->id,
                'name' => $receiver->name,
                'account_number' => $receiver->account_number,
                'type' => $type === 'dealer' ? 'Dealer' : 'Agent',
            ],
        ]);
    }


    public function withdrawRequest(Request $request)
    {
        $user = Auth::user();

        // Logged-in employee and ledger
        $employee = Employee::with('ledger')
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Find this employee's beneficiary
        $beneficiary = Beneficiary::where(
            'reference_type',
            USER_TYPE_EMPLOYEE
        )
            ->where('reference_id', $employee->id)
            ->first();

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Please add a beneficiary before making a withdrawal.',
            ], 422);
        }

        // Validate request
        $validated = $request->validate([
            'accountant_id' => 'required|integer|exists:employees,id',
            'method' => 'required|in:' . implode(',', [
                WITHDRAW_TYPE_CASH,
                WITHDRAW_TYPE_BANK,
                WITHDRAW_TYPE_MOBILE_BANKING,
            ]),
            'amount' => 'required|numeric|min:1',
            'mobile_banking_type' => [
                'nullable',
                'required_if:method,' . WITHDRAW_TYPE_MOBILE_BANKING,
                'in:' . implode(',', [
                    MOBILE_BANKING_TYPE_BKASH,
                    MOBILE_BANKING_TYPE_NAGAD,
                    MOBILE_BANKING_TYPE_ROCKET,
                ]),
            ],
            'note' => 'nullable|string|max:1000',
        ]);

        // Check employee wallet balance
        $balance = 0;

        if ($employee->ledger) {
            $balance = (float) $employee->ledger->entries()->sum('credit')
                - (float) $employee->ledger->entries()->sum('debit');
        }

        $amount = (float) $validated['amount'];

        if ($amount > $balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance.',
            ], 422);
        }

        // Prepare withdrawal data
        $data = [
            'accountant_id' => (int) $validated['accountant_id'],
        ];

        // Bank withdrawal: use saved beneficiary bank details
        if ((int) $validated['method'] === WITHDRAW_TYPE_BANK) {
            if (
                !$beneficiary->bank_name ||
                !$beneficiary->branch_name ||
                !$beneficiary->account_number
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank name, branch and account number must be saved in your beneficiary.',
                ], 422);
            }

            $data['bank_name'] = $beneficiary->bank_name;
            $data['bank_branch'] = $beneficiary->branch_name;
            $data['account_number'] = $beneficiary->account_number;
        }

        // Mobile banking: use the selected provider's number
        if ((int) $validated['method'] === WITHDRAW_TYPE_MOBILE_BANKING) {
            $mobileType = (int) $validated['mobile_banking_type'];

            $mobileNumbers = [
                MOBILE_BANKING_TYPE_BKASH => $beneficiary->bkash_number,
                MOBILE_BANKING_TYPE_NAGAD => $beneficiary->nagad_number,
                MOBILE_BANKING_TYPE_ROCKET => $beneficiary->rocket_number,
            ];

            $phoneNumber = $mobileNumbers[$mobileType] ?? null;

            if (!$phoneNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected mobile banking number is not saved in your beneficiary.',
                ], 422);
            }

            $data['mobile_banking_type'] = $mobileType;
            $data['phone_number'] = $phoneNumber;
        }

        // Create withdrawal request using the new reference structure
        $withdrawal = WithdrawRequest::create([
            'reference_id' => $employee->id,
            'reference_type' => USER_TYPE_EMPLOYEE,
            'method' => $validated['method'],
            'amount' => $amount,
            'data' => $data,
            'note' => $validated['note'] ?? null,
            'status' => 0,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully!',
            'withdrawal_id' => $withdrawal->id,
        ]);
    }

    public function statement()
    {
        $user = Auth::user();

        $employee = Employee::with(['ledger.entries' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }])->where('user_id', $user->id)->first();

        $balance = 0;
        $entries = [];

        if ($employee && $employee->ledger) {
            $balance = $employee->ledger->currentEmployeeBalance();
            $entries = $employee->ledger->entries;
        }

        return view('employee.statement', compact(
            'employee',
            'entries',
            'balance'
        ));
    }

    public function viewWithdrawRequests()
    {
        $user = Auth::user();

        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $withdrawRequests = $employee->withdrawRequests()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.withdraw_requests', compact('employee', 'withdrawRequests'));
    }


    public function transferBalance(
        Request $request,
        \App\Services\AccountingService $accountingService
    ) {
        $validated = $request->validate([
            'transfer_type' => 'required|in:employee,dealer,agent',
            'account_number' => 'required|string|max:50',
            'receiver_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01|decimal:0,2',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $result = DB::transaction(function () use (
                $validated,
                $accountingService
            ) {
                $sender = Employee::with('ledger')
                    ->where('user_id', Auth::id())
                    ->where('status', 1)
                    ->lockForUpdate()
                    ->first();

                if (!$sender || !$sender->ledger_id || !$sender->ledger) {
                    throw new \RuntimeException(
                        'আপনার Employee Wallet Ledger পাওয়া যায়নি।'
                    );
                }

                // Find the receiver according to the selected transfer type.
                if ($validated['transfer_type'] === 'employee') {
                    $receiver = Employee::with('ledger')
                        ->where('account_number', trim($validated['account_number']))
                        ->where('status', 1)
                        ->lockForUpdate()
                        ->first();

                    if (
                        !$receiver ||
                        (int) $receiver->id !== (int) $validated['receiver_id']
                    ) {
                        throw new \RuntimeException(
                            'Receiver Employee account যাচাই করা যায়নি।'
                        );
                    }

                    if ((int) $sender->id === (int) $receiver->id) {
                        throw new \RuntimeException(
                            'নিজের Account-এ টাকা Transfer করা যাবে না।'
                        );
                    }
                } else {
                    $partnerType = $validated['transfer_type'] === 'dealer' ? 1 : 2;

                    $receiver = Partner::with('ledger')
                        ->where('account_number', trim($validated['account_number']))
                        ->where('type', $partnerType)
                        ->where('status', 1)
                        ->lockForUpdate()
                        ->first();

                    if (
                        !$receiver ||
                        (int) $receiver->id !== (int) $validated['receiver_id']
                    ) {
                        throw new \RuntimeException(
                            'Receiver Dealer/Agent account যাচাই করা যায়নি।'
                        );
                    }
                }

                if (!$receiver->ledger_id || !$receiver->ledger) {
                    throw new \RuntimeException(
                        'Receiver account-এর Ledger তৈরি করা নেই।'
                    );
                }

                if ((int) $sender->ledger_id === (int) $receiver->ledger_id) {
                    throw new \RuntimeException(
                        'Sender ও Receiver-এর Ledger একই।'
                    );
                }

                $amount = (float) $validated['amount'];

                // Use the existing Employee wallet balance calculation.
                $balance = (float) $sender->ledger->currentEmployeeBalance();

                if ($amount > $balance) {
                    throw new \RuntimeException(
                        'আপনার Wallet-এ পর্যাপ্ত Balance নেই।'
                    );
                }

                $receiverName = $validated['transfer_type'] === 'employee'
                    ? $receiver->full_name
                    : $receiver->name;

                $userNote = trim($validated['note'] ?? '');

                $note = "{$sender->full_name} transferred {$amount} to {$receiverName}";

                if ($userNote !== '') {
                    $note .= " | Note: {$userNote}";
                }

                // Match the existing project's AccountingService signature.
                $accountingService->addTransaction(
                    amount: $amount,
                    debitLedgerId: $sender->ledger_id,
                    creditLedgerId: $receiver->ledger_id,
                    voucherType: VT_EMPLOYEE_TRANSFER,
                    referenceId: $receiver->id,
                    note: $note
                );

                return [
                    'amount' => $amount,
                    'receiver_name' => $receiverName,
                    'receiver_account' => $receiver->account_number,
                ];
            }, 3);

            return response()->json([
                'success' => true,
                'message' => '৳' . number_format($result['amount'], 2)
                    . ' Transfer successful. Receiver: '
                    . $result['receiver_name']
                    . ' (' . $result['receiver_account'] . ')',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            report($e);

            \Log::error('Employee balance transfer failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Transfer ব্যর্থ হয়েছে। Laravel log পরীক্ষা করুন।',
            ], 500);
        }
    }
}
