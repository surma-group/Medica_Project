<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Holiday;
use App\Models\Employee;
use App\Models\Partner;
use App\Models\WithdrawRequest;
use App\Models\Beneficiary;
use App\Models\MonthlySalary;
use App\Models\PaymentAccount;
use App\Models\AddMoneyRequest;
use App\Services\AccountingService;

class PartnerController extends Controller
{
    public function dashboard()
    {
        $partner = Auth::user();

        return view('partner.dashboard', compact('partner'));
    }

    public function profile()
    {
        $user = Auth::user();

        $partner = $user->employee()
            ->with(['designation', 'department', 'company'])
            ->first();

        return view('partner.profile', compact('user', 'partner'));
    }

    public function calendar()
    {
        return view('partner.calendar');
    }

    public function calendarEvents()
    {
        $holidays = Holiday::where('status', 1)->get();

        $events = [];

        foreach ($holidays as $holiday) {
            $events[] = [
                'title' => $holiday->title,
                'start' => $holiday->from,
                'end'   => $holiday->to,
                'backgroundColor' => '#00bcd4',
                'borderColor' => '#00bcd4',
            ];
        }

        return response()->json($events);
    }


    public function statement()
    {
        $user = Auth::user();

        $partner = Partner::with([
            'ledger.entries' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $balance = 0;
        $entries = collect();

        if ($partner->ledger) {
            $balance = $partner->ledger->entries()->sum('credit')
                - $partner->ledger->entries()->sum('debit');

            $entries = $partner->ledger->entries;
        }

        return view('partner.statement', compact(
            'partner',
            'entries',
            'balance'
        ));
    }


    public function salary()
    {
        $user = Auth::user();

        $partner = Employee::with([
            'designation',
            'department',
            'company',
            'monthlySalaries.details'
        ])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $salaries = $partner->monthlySalaries()
            ->orderBy('salary_month', 'desc')
            ->get();

        return view('partner.salary', compact('partner', 'salaries'));
    }

    public function salaryDetails($id)
    {
        $user = Auth::user();

        $partner = Employee::with(['designation', 'department', 'company'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $salary = MonthlySalary::with([
            'details.component',
            'generator',
            'approver'
        ])
            ->where('employee_id', $partner->id)
            ->findOrFail($id);

        return view('partner.salary_details', compact('partner', 'salary'));
    }

    public function wallet()
    {
        $user = Auth::user();

        $partner = Partner::with([
            'ledger.entries' => function ($query) {
                $query->latest();
            }
        ])->where('user_id', $user->id)->first();

        $balance = 0;
        $entries = [];
        $beneficiary = null;

        $paymentAccounts = PaymentAccount::where('status', 1)->get();

        if ($partner) {

            // Calculate partner balance and load ledger entries
            if ($partner->ledger) {
                $balance = $partner->ledger->entries()->sum('credit')
                    - $partner->ledger->entries()->sum('debit');

                $entries = $partner->ledger->entries;
            }

            // Load partner beneficiary information
            $referenceType = ($partner->type == 1)
                ? USER_TYPE_DEALER
                : USER_TYPE_AGENT;

            $beneficiary = Beneficiary::where('reference_id', $partner->id)
                ->where('reference_type', $referenceType)
                ->first();
        }

        return view('partner.wallet', compact(
            'partner',
            'balance',
            'entries',
            'paymentAccounts',
            'beneficiary'
        ));
    }


    public function addMoney(Request $request)
    {
        $request->validate([
            'method' => 'required|exists:payment_accounts,id',
            'amount' => 'required|numeric|min:1',
            'transaction_id' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        AddMoneyRequest::create([
            'user_id' => auth()->id(),
            'payment_account_id' => $request->method,
            'amount' => $request->amount,
            'transaction_id' => $request->transaction_id,
            'note' => $request->note,
            'status' => 0, // pending
        ]);

        return response()->json([
            'message' => 'Add money request submitted successfully'
        ]);
    }


    public function viewWithdrawRequests()
    {
        $user = Auth::user();

        $partner = Partner::where('user_id', $user->id)
            ->firstOrFail();

        // Determine stakeholder reference type
        $referenceType = match ((int) $partner->type) {
            1 => USER_TYPE_DEALER,
            2 => USER_TYPE_AGENT,
            default => null,
        };

        if ($referenceType === null) {
            abort(403, 'Invalid partner type.');
        }

        // Get partner withdrawal requests
        $withdrawRequests = WithdrawRequest::where('reference_id', $partner->id)
            ->where('reference_type', $referenceType)
            ->orderByDesc('created_at')
            ->get();

        return view('partner.withdraw_requests', compact(
            'partner',
            'withdrawRequests'
        ));
    }


    public function withdrawRequest(Request $request)
    {
        $user = Auth::user();

        // Get logged-in partner
        $partner = Partner::where('user_id', $user->id)
            ->firstOrFail();

        // Determine stakeholder reference type
        $referenceType = match ((int) $partner->type) {
            1 => USER_TYPE_DEALER,
            2 => USER_TYPE_AGENT,
            default => null,
        };

        if ($referenceType === null) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid partner type.',
            ], 422);
        }

        // Find partner beneficiary
        $beneficiary = Beneficiary::where('reference_id', $partner->id)
            ->where('reference_type', $referenceType)
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

        // Check partner wallet balance
        $partner = Partner::with('ledger')
            ->findOrFail($partner->id);

        $balance = 0;

        if ($partner->ledger) {
            $balance = (float) $partner->ledger->entries()->sum('credit')
                - (float) $partner->ledger->entries()->sum('debit');
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

        // Mobile banking: use the selected provider's saved number
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

        // Create withdrawal request
        $withdrawal = WithdrawRequest::create([
            'reference_id' => $partner->id,
            'reference_type' => $referenceType,
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

    public function beneficiaries()
    {
        $user = Auth::user();

        $partner = Partner::where('user_id', $user->id)
            ->firstOrFail();

        $referenceType = $partner->type == 1
            ? USER_TYPE_DEALER
            : USER_TYPE_AGENT;

        $beneficiary = Beneficiary::where('reference_id', $partner->id)
            ->where('reference_type', $referenceType)
            ->first();

        return view('partner.beneficiaries', compact(
            'partner',
            'beneficiary'
        ));
    }


    public function storeBeneficiary(Request $request)
    {
        $user = Auth::user();

        $partner = Partner::where('user_id', $user->id)
            ->firstOrFail();

        // Dealer = 2, Agent = 3
        $referenceType = $partner->type == 1
            ? USER_TYPE_DEALER
            : USER_TYPE_AGENT;

        $validated = $request->validate([
            'bank_name'      => 'nullable|string|max:255',
            'branch_name'    => 'nullable|string|max:255',
            'account_type'   => 'nullable|in:1,2',
            'account_number' => 'nullable|string|max:255',
            'bkash_number'   => 'nullable|string|max:255',
            'rocket_number'  => 'nullable|string|max:255',
            'nagad_number'   => 'nullable|string|max:255',
        ]);

        $beneficiary = Beneficiary::where('reference_id', $partner->id)
            ->where('reference_type', $referenceType)
            ->first();

        if ($beneficiary) {
            $beneficiary->update($validated);

            return back()->with(
                'success',
                'Beneficiary updated successfully.'
            );
        }

        Beneficiary::create(array_merge($validated, [
            'reference_id'   => $partner->id,
            'reference_type' => $referenceType,
        ]));

        return back()->with(
            'success',
            'Beneficiary added successfully.'
        );
    }



    public function searchAccountant($accountantId)
    {
        $accountant = Employee::with([
            'user',
            'branch',
            'designation',
        ])
            ->where('account_number', $accountantId)
            ->first();

        if (!$accountant) {
            return response()->json([
                'success' => false,
            ], 404);
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
        $validated = $request->validate([
            'transfer_type' => 'required|in:employee,dealer,agent',
            'account_number' => 'required|string|max:50',
        ]);

        $sender = Partner::where('user_id', Auth::id())
            ->where('status', 1)
            ->first();

        if (!$sender) {
            return response()->json([
                'success' => false,
                'message' => 'আপনার Active Partner account পাওয়া যায়নি।',
            ], 403);
        }

        $accountNumber = trim($validated['account_number']);
        $type = $validated['transfer_type'];

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

        // Partner type: 1 = Dealer, 2 = Agent
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
                'name' => $receiver->name,
                'account_number' => $receiver->account_number,
                'type' => $type === 'dealer' ? 'Dealer' : 'Agent',
            ],
        ]);
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
                // Logged-in Partner
                $sender = Partner::with('ledger')
                    ->where('user_id', Auth::id())
                    ->where('status', 1)
                    ->lockForUpdate()
                    ->first();

                if (!$sender || !$sender->ledger_id || !$sender->ledger) {
                    throw new \RuntimeException(
                        'আপনার Partner Wallet Ledger পাওয়া যায়নি।'
                    );
                }

                $accountNumber = trim($validated['account_number']);
                $type = $validated['transfer_type'];

                // Find receiver using selected transfer type
                if ($type === 'employee') {
                    $receiver = Employee::with('ledger')
                        ->where('account_number', $accountNumber)
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
                } else {
                    $partnerType = $type === 'dealer' ? 1 : 2;

                    $receiver = Partner::with('ledger')
                        ->where('account_number', $accountNumber)
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

                    if ((int) $sender->id === (int) $receiver->id) {
                        throw new \RuntimeException(
                            'নিজের Account-এ টাকা Transfer করা যাবে না।'
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

                // Partner wallet balance = total credit - total debit
                $balance = (float) $sender->ledger->entries()->sum('credit')
                    - (float) $sender->ledger->entries()->sum('debit');

                if ($amount > $balance) {
                    throw new \RuntimeException(
                        'আপনার Wallet-এ পর্যাপ্ত Balance নেই।'
                    );
                }

                $receiverName = $type === 'employee'
                    ? $receiver->full_name
                    : $receiver->name;

                $userNote = trim($validated['note'] ?? '');

                $note = "{$sender->name} transferred {$amount} to {$receiverName}";

                if ($userNote !== '') {
                    $note .= " | Note: {$userNote}";
                }

                // Existing AccountingService transaction method
                $accountingService->addTransaction(
                    amount: $amount,
                    debitLedgerId: $sender->ledger_id,
                    creditLedgerId: $receiver->ledger_id,
                    voucherType: VT_PARTNER_TRANSFER,
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

            \Log::error('Partner balance transfer failed', [
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
