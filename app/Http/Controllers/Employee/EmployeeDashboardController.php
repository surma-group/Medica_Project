<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Employee;
use App\Models\PaymentAccount;
use App\Models\AddMoneyRequest;
use App\Models\WithdrawRequest;
use App\Models\Beneficiary;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        return view('employee.dashboard');
    }


    public function wallet()
    {
        $user = Auth::user();

        $employee = Employee::with([
            'ledger.entries' => function ($query) {
                $query->latest();
            }
        ])->where('user_id', $user->id)->firstOrFail();

        // Load logged-in employee's beneficiary
        $beneficiary = Beneficiary::where(
            'reference_type',
            USER_TYPE_EMPLOYEE
        )
            ->where('reference_id', $employee->id)
            ->first();

        $balance = 0;
        $entries = [];

        $paymentAccounts = PaymentAccount::where('status', 1)->get();

        if ($employee->ledger) {
            $balance = $employee->ledger->entries()->sum('credit')
                - $employee->ledger->entries()->sum('debit');

            $entries = $employee->ledger->entries;
        }

        return view('employee.wallet', compact(
            'employee',
            'balance',
            'entries',
            'paymentAccounts',
            'beneficiary'
        ));
    }
}
