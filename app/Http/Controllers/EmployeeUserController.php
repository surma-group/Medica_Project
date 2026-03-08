<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Holiday;
use App\Models\Employee;
use App\Models\WithdrawRequest;
use App\Models\Beneficiary;
use App\Models\MonthlySalary;

class EmployeeUserController extends Controller
{
    public function dashboard()
    {
        $employee = Auth::user();
        return view('frontend.employee.dashboard', compact('employee'));
    }

    public function profile()
    {
        $user = Auth::user();
        $employee = $user->employee()
            ->with(['designation', 'department', 'company'])
            ->first();
        return view('frontend.employee.profile', compact('user', 'employee'));
    }
    public function calendar()
    {
        return view('frontend.calendar');
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

        $employee = Employee::with(['ledger.entries' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->where('user_id', $user->id)->first();

        $balance = 0;
        $entries = [];

        if ($employee && $employee->ledger) {
            $balance = $employee->ledger->currentEmployeeBalance();
            $entries = $employee->ledger->entries;
        }

        return view('frontend.employee.statement', compact(
            'employee',
            'entries',
            'balance'
        ));
    }

    public function salary()
    {
        $user = Auth::user();

        $employee = Employee::with([
            'designation',
            'department',
            'company',
            'monthlySalaries.details' // optional
        ])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $salaries = $employee->monthlySalaries()
            ->orderBy('salary_month', 'desc')
            ->get();

        return view('frontend.employee.salary', compact('employee', 'salaries'));
    }

    // Show details of a specific salary
    public function salaryDetails($id)
    {
        $user = Auth::user();

        $employee = Employee::with(['designation', 'department', 'company'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $salary = MonthlySalary::with([
            'details.component',
            'generator',
            'approver'
        ])
            ->where('employee_id', $employee->id)
            ->findOrFail($id);

        return view('frontend.employee.salary_details', compact('employee', 'salary'));
    }


    public function wallet()
    {
        $user = Auth::user();

        // Get employee with ledger entries
        $employee = Employee::with(['ledger.entries' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->where('user_id', $user->id)->first();

        $balance = 0;
        $entries = [];
        $withdrawRequests = collect(); // default empty collection
        $beneficiary = null; // default

        if ($employee) {
            // Ledger entries
            if ($employee->ledger) {
                $balance = $employee->ledger->currentEmployeeBalance();
                $entries = $employee->ledger->entries;
            }

            // Current employee's withdraw requests
            $withdrawRequests = $employee->withdrawRequests()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Get beneficiary info (for Mobile Banking / Bank display)
            $beneficiary = $employee->beneficiaries()->first(); // assuming one beneficiary per employee
        }

        return view('frontend.employee.wallet', compact(
            'employee',
            'balance',
            'entries',
            'withdrawRequests',
            'beneficiary' // <-- send to Blade
        ));
    }

    public function viewWithdrawRequests()
    {
        $user = Auth::user();

        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $withdrawRequests = $employee->withdrawRequests()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.employee.withdraw_requests', compact('employee', 'withdrawRequests'));
    }

    public function withdrawRequest(Request $request)
    {
        $user = Auth::user();

        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        $beneficiary = Beneficiary::where('employee_id', $employee->id)->first();

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Please add a beneficiary before making a withdrawal.'
            ], 400);
        }

        $request->validate([
            'accountant_id' => 'required|exists:employees,id',
            'method' => 'required|in:' . implode(',', [
                WITHDRAW_TYPE_CASH,
                WITHDRAW_TYPE_BANK,
                WITHDRAW_TYPE_MOBILE_BANKING
            ]),
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string',
        ]);

        $data = [
            'accountant_id' => $request->accountant_id,
        ];

        // Mobile Banking
        if ($request->method == WITHDRAW_TYPE_MOBILE_BANKING) {
            if ($beneficiary->bkash_number) {
                $data['mobile_banking_type'] = MOBILE_BANKING_TYPE_BKASH;
                $data['phone_number'] = $beneficiary->bkash_number;
            } elseif ($beneficiary->nagad_number) {
                $data['mobile_banking_type'] = MOBILE_BANKING_TYPE_NAGAD;
                $data['phone_number'] = $beneficiary->nagad_number;
            } elseif ($beneficiary->rocket_number) {
                $data['mobile_banking_type'] = MOBILE_BANKING_TYPE_ROCKET;
                $data['phone_number'] = $beneficiary->rocket_number;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No mobile banking number found in beneficiary.'
                ], 400);
            }
        }

        // Bank
        if ($request->method == WITHDRAW_TYPE_BANK) {
            if (!$beneficiary->bank_name || !$beneficiary->account_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank information is missing in your beneficiary.'
                ], 400);
            }

            $data['bank_name'] = $beneficiary->bank_name;
            $data['bank_branch'] = $beneficiary->branch_name;
            $data['account_number'] = $beneficiary->account_number;
        }
        // Save withdrawal
        $withdrawal = WithdrawRequest::create([
            'employee_id' => $employee->id,
            'method' => $request->method,
            'amount' => $request->amount,
            'data' => $data,
            'note' => $request->note,
            'status' => 0,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully!',
            'withdrawal_id' => $withdrawal->id
        ]);
    }

    public function beneficiaries(Request $request)
    {
        $user = Auth::user();

        // Get the employee record linked to the authenticated user
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        // Fetch the single beneficiary for this employee (or null if none)
        $beneficiary = Beneficiary::where('employee_id', $employee->id)->first();

        return view('frontend.employee.beneficiaries', compact('beneficiary'));
    }

    public function storeBeneficiary(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'bank_name'      => 'nullable|string',
            'branch_name'    => 'nullable|string',
            'account_type'   => 'nullable|in:1,2',
            'account_number' => 'nullable|string',
            'bkash_number'   => 'nullable|string',
            'rocket_number'  => 'nullable|string',
            'nagad_number'   => 'nullable|string',
        ]);

        // Check if beneficiary exists
        $beneficiary = Beneficiary::where('employee_id', $employee->id)->first();

        if ($beneficiary) {
            // Update existing
            $beneficiary->update($validated);
            return back()->with('success', 'Beneficiary updated successfully.');
        } else {
            // Create new
            Beneficiary::create(array_merge($validated, [
                'employee_id' => $employee->id,
            ]));
            return back()->with('success', 'Beneficiary added successfully.');
        }
    }

    public function searchAccountant($accountantId)
    {
        // Lookup accountant by employee_code
        $accountant = Employee::with(['user', 'branch', 'designation'])
            ->where('employee_code', $accountantId)
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
}
