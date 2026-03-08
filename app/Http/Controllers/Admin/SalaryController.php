<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SalaryStructure;
use App\Models\SalaryStructureDetail;
use App\Models\SalaryComponent;

class SalaryController extends Controller
{
    public function salaryStructureStore(Request $request)
    {
        $request->validate([
            'employee_id'  => 'required|integer',
            'basic_salary' => 'required|numeric|gt:0',
            'components'   => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {

            $employeeId  = $request->employee_id;
            $basicSalary = $request->basic_salary;
            $components  = $request->components ?? [];

            $totalEarning   = 0;
            $totalDeduction = 0;

            // 🔹 Create or Update Salary Structure
            $salaryStructure = SalaryStructure::updateOrCreate(
                ['employee_id' => $employeeId],
                [
                    'basic_salary'    => $basicSalary,
                    'total_earning'   => 0,
                    'total_deduction' => 0,
                    'net_salary'      => 0,
                    'created_by'      => auth()->id(),
                ]
            );

            // 🔹 Remove old details (for update case)
            SalaryStructureDetail::where(
                'salary_structure_id',
                $salaryStructure->id
            )->delete();

            // 🔹 Loop Components
            foreach ($components as $componentId => $value) {

                $component = SalaryComponent::find($componentId);
                if (!$component) continue;

                // Calculate amount
                if ($component->amount_type == 2) {
                    // Percentage
                    $calculatedAmount = ($basicSalary * $component->amount) / 100;
                } else {
                    // Fixed
                    $calculatedAmount = $component->amount;
                }

                // Sum totals
                if ($component->payment_type == 1) {
                    $totalEarning += $calculatedAmount;
                } else {
                    $totalDeduction += $calculatedAmount;
                }

                // Save detail
                SalaryStructureDetail::create([
                    'salary_structure_id' => $salaryStructure->id,
                    'salary_component_id' => $component->id,
                    'component_type'      => $component->payment_type,
                    'amount_type'         => $component->amount_type,
                    'amount'              => $component->amount,
                    'calculated_amount'   => $calculatedAmount,
                ]);
            }

            // 🔹 Final Net Salary
            $netSalary = $basicSalary + $totalEarning - $totalDeduction;

            // 🔹 Update totals
            $salaryStructure->update([
                'total_earning'   => $totalEarning,
                'total_deduction' => $totalDeduction,
                'net_salary'      => $netSalary,
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Salary structure saved successfully'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function salaryStructureShow($employeeId)
    {
        $salary = SalaryStructure::with('details')
            ->where('employee_id', $employeeId)
            ->first();

        if (!$salary) {
            return response()->json([
                'status' => false
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $salary
        ]);
    }
}
