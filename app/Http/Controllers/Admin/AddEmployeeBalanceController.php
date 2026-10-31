<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Ledger;
use App\Models\VoucherEntry;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AddEmployeeBalanceController extends Controller
{
    /**
     * Display employee balance form and history.
     */
    public function index()
    {
        $employees = Employee::with(['ledger', 'designation'])
            ->where('status', 1)
            ->orderBy('full_name')
            ->get();

        $cashBalance = Ledger::find(L_CASH_DESK);
        $bankBalance = Ledger::find(L_BANK_MAIN);

        $history = VoucherEntry::with(['entries.ledger'])
            ->where('type', VT_EMPLOYEE_BALANCE_ADD)
            ->whereNotNull('reference_id')
            ->latest('id')
            ->paginate(15);

        $employeeIds = $history->getCollection()
            ->pluck('reference_id')
            ->filter()
            ->unique()
            ->values();

        $historyEmployees = Employee::with('designation')
            ->whereIn('id', $employeeIds)
            ->get()
            ->keyBy('id');

        return view(
            'admin.add_employee_balance.index',
            compact(
                'employees',
                'cashBalance',
                'bankBalance',
                'history',
                'historyEmployees'
            )
        );
    }

    /**
     * Admin directly adds money to an employee wallet.
     */
    public function store(
        Request $request,
        AccountingService $accountingService
    ) {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('employees', 'id')
                    ->where('status', 1),
            ],
            'source_account' => [
                'required',
                'integer',
                Rule::in([
                    L_CASH_DESK,
                    L_BANK_MAIN,
                ]),
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999999',
            ],
            'note' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        try {
            DB::transaction(function () use (
                $validated,
                $accountingService
            ) {
                $employee = Employee::where('status', 1)
                    ->lockForUpdate()
                    ->findOrFail($validated['employee_id']);

                if (!$employee->ledger_id) {
                    throw new \RuntimeException(
                        'এই কর্মচারীর Ledger তৈরি করা নেই।'
                    );
                }

                $employeeLedger = Ledger::whereKey(
                    $employee->ledger_id
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                $amount = round(
                    (float) $validated['amount'],
                    2
                );

                $sourceLedger = Ledger::whereKey(
                    (int) $validated['source_account']
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($sourceLedger->id === $employeeLedger->id) {
                    throw new \RuntimeException(
                        'টাকার উৎস এবং কর্মচারীর Ledger একই হতে পারবে না।'
                    );
                }

                // Cash/Bank available balance = Debit - Credit.
                $availableBalance =
                    (float) $sourceLedger->entries()->sum('debit')
                    - (float) $sourceLedger->entries()->sum('credit');

                if ($amount > $availableBalance) {
                    throw new \RuntimeException(
                        'নির্বাচিত Cash/Bank Account-এ পর্যাপ্ত ব্যালেন্স নেই।'
                    );
                }

                /*
                 * Employee Wallet balance = Debit - Credit.
                 *
                 * Employee Wallet: Debit -> balance increases.
                 * Cash/Bank: Credit -> balance decreases.
                 *
                 * Both entries are recorded through AccountingService.
                 */
                $accountingService->addTransaction(
                    amount: $amount,
                    debitLedgerId: $employeeLedger->id,
                    creditLedgerId: $sourceLedger->id,
                    voucherType: VT_EMPLOYEE_BALANCE_ADD,
                    referenceId: $employee->id,
                    note: $validated['note']
                        ?? 'Employee wallet balance added',
                    createdBy: auth('admin')->id()
                );
            });

            return redirect()
                ->route('add_employee_balance.index')
                ->with(
                    'success',
                    'কর্মচারীর Wallet-এ টাকা সফলভাবে যোগ করা হয়েছে।'
                );

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    $e instanceof \RuntimeException
                        ? $e->getMessage()
                        : 'ব্যালেন্স যোগ করা যায়নি। আবার চেষ্টা করুন।'
                );
        }
    }
}
