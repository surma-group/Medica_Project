<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\SalaryComponent;
use App\Models\MonthlySalary;
use App\Models\MonthlySalaryDetail;
use App\Models\SalaryStructure;
use App\Models\SalaryStructureDetail;
use App\Models\Ledger;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryGenerateController extends Controller
{
    /**
     * Display a listing of the monthly salaries (index page)
     */
    public function index()
    {
        $monthlySalaries = MonthlySalary::select(
            'salary_month',
            DB::raw('COUNT(DISTINCT employee_id) as employee_count'),
            DB::raw('SUM(total_earning) as total_earning'),
            DB::raw('SUM(total_deduction) as total_deduction'),
            DB::raw('SUM(net_salary) as net_salary'),
            DB::raw('MAX(status) as status')
        )
            ->groupBy('salary_month')
            ->orderByDesc('salary_month')
            ->paginate(10); // ✅ IMPORTANT

        return view('admin.salary_generate.index', compact('monthlySalaries'));
    }

    /**
     * Show the form for creating a new monthly salary
     */
    public function create()
    {
        $employees = Employee::where('status', 1)->get();
        $salaryComponents = SalaryComponent::all();

        return view('admin.salary_generate.create', compact('employees', 'salaryComponents'));
    }

    /**
     * Store newly generated monthly salary
     */
    public function store(Request $request)
    {
        $request->validate([
            'salary_month' => 'required|date',
            'employees'    => 'required|array',
        ]);

        $salaryMonth = Carbon::parse($request->salary_month)->startOfMonth();

        // Check if salary already generated for this month
        $exists = MonthlySalary::where('salary_month', $salaryMonth)->exists();
        if ($exists) {
            $monthName = $salaryMonth->format('F Y');
            return redirect()->back()->with('error', "Salary for {$monthName} has already been generated.");
        }

        DB::beginTransaction();

        try {
            $employees = Employee::where('status', 1)->get();

            foreach ($employees as $employee) {
                $empData = $request->employees[$employee->id] ?? null;
                $structure = $employee->salaryStructure ?? null;

                if (!$structure || !$empData) continue;

                // Use the updated values from the form
                $basicSalary = $empData['basic_salary'] ?? 0;

                // Calculate total earnings
                $totalEarning = 0;
                if (isset($empData['earnings']) && is_array($empData['earnings'])) {
                    foreach ($empData['earnings'] as $compId => $amount) {
                        // Only include if checkbox is selected
                        if (!isset($empData['earnings_selected'][$compId])) continue;
                        $totalEarning += (float) $amount;
                    }
                }

                // Calculate total deductions
                $totalDeduction = 0;
                if (isset($empData['deductions']) && is_array($empData['deductions'])) {
                    foreach ($empData['deductions'] as $compId => $amount) {
                        // Only include if checkbox is selected
                        if (!isset($empData['deductions_selected'][$compId])) continue;
                        $totalDeduction += (float) $amount;
                    }
                }

                $netSalary = $basicSalary + $totalEarning - $totalDeduction;

                // Create Monthly Salary
                $monthlySalary = MonthlySalary::create([
                    'employee_id'     => $employee->id,
                    'salary_month'    => $salaryMonth,
                    'basic_salary'    => $basicSalary,
                    'total_earning'   => $totalEarning,
                    'total_deduction' => $totalDeduction,
                    'net_salary'      => $netSalary,
                    'status'          => 'pending',
                    'generated_by'    => auth()->id(),
                ]);

                // Save details
                foreach ($structure->details as $detail) {
                    $compId = $detail->salary_component_id;
                    $amount = null;
                    if ($detail->component_type == 1 && isset($empData['earnings'][$compId])) {
                        // Earning
                        if (!isset($empData['earnings_selected'][$compId])) continue;
                        $amount = $empData['earnings'][$compId];
                    } elseif ($detail->component_type == 2 && isset($empData['deductions'][$compId])) {
                        // Deduction
                        if (!isset($empData['deductions_selected'][$compId])) continue;
                        $amount = $empData['deductions'][$compId];
                    }

                    if ($amount === null) continue;

                    MonthlySalaryDetail::create([
                        'monthly_salary_id'   => $monthlySalary->id,
                        'salary_component_id' => $compId,
                        'component_type'      => $detail->component_type,
                        'amount_type'         => $detail->amount_type,
                        'amount'              => $detail->amount,
                        'calculated_amount'   => $amount,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('salary_generate.index')
                ->with('success', 'Monthly salary generated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show single monthly salary details
     */
    public function show($month)
    {
        // Parse the month and set to first day
        $salaryMonth = Carbon::parse($month)->startOfMonth();

        // Get all salaries for this month with employee relation
        $salaries = MonthlySalary::with('employee')
            ->where('salary_month', $salaryMonth)
            ->get();

        if ($salaries->isEmpty()) {
            abort(404, 'No salary data found for this month.');
        }

        // Get status from first salary (assuming all have same status for the month)
        $status = $salaries->first()->status;

        return view('admin.salary_generate.show', compact('salaries', 'salaryMonth', 'status'));
    }

    public function approve_old($month)
    {
        $salaries = MonthlySalary::with('employee')
            ->where('salary_month', $month)
            ->where('status', 'pending')
            ->get();

        if ($salaries->isEmpty()) {
            return redirect()->back()->with('error', 'No pending salaries for this month.');
        }

        $accountingService = app(\App\Services\AccountingService::class);

        DB::transaction(function () use ($salaries, $accountingService) {
            foreach ($salaries as $salary) {
                $employee = $salary->employee;

                // Ensure employee has a ledger
                if (!$employee->ledger_id) {
                    $ledger = Ledger::create([
                        'chart_account_id' => 7, // Salary Expense chart account id
                        'title' => "Salary Account - " . $employee->full_name,
                        'code' => "EMP" . str_pad($employee->id, 4, '0', STR_PAD_LEFT),
                        'type' => 1, // Asset
                        'for_income' => 0,
                        'for_expense' => 0,
                        'reference_id' => $employee->id,
                    ]);

                    // Update employee with ledger_id
                    $employee->update(['ledger_id' => $ledger->id]);
                } else {
                    $ledger = $employee->ledger; // Use existing ledger
                }

                // Salary Expense Ledger (company)
                $salaryExpenseLedgerId = L_SALARY_EXPENSE;

                // Approve salary: Debit company expense, Credit employee ledger
                $accountingService->addTransaction(
                    amount: $salary->net_salary,
                    debitLedgerId: $salaryExpenseLedgerId,
                    creditLedgerId: $ledger->id,
                    voucherType: VT_SALARY_APPROVE,
                    referenceId: $employee->id,
                    note: "Salary approved for " . $employee->full_name
                );

                // Mark salary as approved
                $salary->status = 'paid';
                $salary->save();
            }
        });

        return redirect()->back()->with('success', 'All salaries approved successfully.');
    }

    public function approve($month)
    {
        $salaries = MonthlySalary::with(['employee', 'details'])
            ->where('salary_month', $month)
            ->where('status', 'pending')
            ->get();

        if ($salaries->isEmpty()) {
            return redirect()->back()->with('error', 'No pending salaries for this month.');
        }

        $accountingService = app(\App\Services\AccountingService::class);

        DB::transaction(function () use ($salaries, $accountingService) {
            foreach ($salaries as $salary) {
                $employee = $salary->employee;

                // Ensure employee has a ledger
                if (!$employee->ledger_id) {
                    $ledger = Ledger::create([
                        'chart_account_id' => 7, // Salary Expense chart account id
                        'title' => "Salary Account - " . $employee->full_name,
                        'code' => "EMP" . str_pad($employee->id, 4, '0', STR_PAD_LEFT),
                        'type' => 1, // Asset
                        'for_income' => 0,
                        'for_expense' => 0,
                        'reference_id' => $employee->id,
                    ]);

                    $employee->update(['ledger_id' => $ledger->id]);
                } else {
                    $ledger = $employee->ledger; // Use existing ledger
                }

                // Salary Expense Ledger (company)
                $salaryExpenseLedgerId = L_SALARY_EXPENSE;

                // Approve salary: Debit company expense, Credit employee ledger
                $accountingService->addTransaction(
                    amount: $salary->net_salary,
                    debitLedgerId: $salaryExpenseLedgerId,
                    creditLedgerId: $ledger->id,
                    voucherType: VT_SALARY_APPROVE,
                    referenceId: $employee->id,
                    note: "Salary approved for " . $employee->full_name
                );

                // Provident Fund transaction (if applicable)
                $pfDetail = $salary->details->firstWhere('salary_component_id', SALARY_COMPONENT_PROVIDENT_FUND);
                if ($pfDetail && $pfDetail->calculated_amount > 0) {
                    $accountingService->addTransaction(
                        amount: $pfDetail->calculated_amount,
                        debitLedgerId: $salaryExpenseLedgerId,       // Debit Salary Expense
                        creditLedgerId: L_PROVIDENT_FUND_PAYABLE,   // Credit PF Payable Ledger
                        voucherType: VT_SALARY_APPROVE,
                        referenceId: $employee->id,
                        note: "Provident Fund contribution for " . $employee->full_name
                    );
                }

                // Mark salary as approved and record approver
                $salary->status = 'paid';
                $salary->approved_by = auth()->id();
                $salary->save();
            }
        });

        return redirect()->back()->with('success', 'All salaries approved successfully.');
    }
}
