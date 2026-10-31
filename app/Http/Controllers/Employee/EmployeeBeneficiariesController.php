<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeBeneficiariesController extends Controller
{
    /**
     * Get the logged-in employee's beneficiary.
     */
    private function getEmployee()
    {
        return Employee::where('user_id', Auth::id())
            ->firstOrFail();
    }

    /**
     * Show employee beneficiary details.
     */
    public function beneficiaries()
    {
        $employee = $this->getEmployee();

        $beneficiary = Beneficiary::where(
            'reference_type',
            USER_TYPE_EMPLOYEE
        )
            ->where('reference_id', $employee->id)
            ->first();

        return view('employee.beneficiaries', compact(
            'employee',
            'beneficiary'
        ));
    }

    /**
     * Store a new beneficiary or update the existing one.
     */
    public function storeBeneficiary(Request $request)
    {
        $employee = $this->getEmployee();

        $validated = $request->validate([
            'bank_name'      => 'nullable|string|max:255',
            'branch_name'    => 'nullable|string|max:255',
            'account_type'   => 'nullable|in:1,2',
            'account_number' => 'nullable|string|max:255',
            'bkash_number'   => 'nullable|string|max:255',
            'rocket_number'  => 'nullable|string|max:255',
            'nagad_number'   => 'nullable|string|max:255',
        ]);

        $beneficiary = Beneficiary::updateOrCreate(
            [
                'reference_type' => USER_TYPE_EMPLOYEE,
                'reference_id'   => $employee->id,
            ],
            $validated
        );

        return back()->with(
            'success',
            $beneficiary->wasRecentlyCreated
                ? 'Beneficiary added successfully.'
                : 'Beneficiary updated successfully.'
        );
    }

    /**
     * Update an existing beneficiary owned by the logged-in employee.
     */
    public function updateBeneficiary(Request $request, $id)
    {
        $employee = $this->getEmployee();

        $beneficiary = Beneficiary::where('id', $id)
            ->where('reference_type', USER_TYPE_EMPLOYEE)
            ->where('reference_id', $employee->id)
            ->firstOrFail();

        $validated = $request->validate([
            'bank_name'      => 'nullable|string|max:255',
            'branch_name'    => 'nullable|string|max:255',
            'account_type'   => 'nullable|in:1,2',
            'account_number' => 'nullable|string|max:255',
            'bkash_number'   => 'nullable|string|max:255',
            'rocket_number'  => 'nullable|string|max:255',
            'nagad_number'   => 'nullable|string|max:255',
        ]);

        $beneficiary->update($validated);

        return back()->with(
            'success',
            'Beneficiary updated successfully.'
        );
    }
}
