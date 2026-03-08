<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmploymentType;
use App\Models\District;
use App\Models\User;
use App\Models\SalaryComponent;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;


class EmployeeController extends Controller
{
    public function index()
    {
        // In EmployeeController index method
        $employees = Employee::with(['company', 'branch', 'department', 'designation'])->get();

        // Get all active salary components
        $salaryComponents = SalaryComponent::where('status', 1)->get();

        return view('admin.employees.index', compact('employees', 'salaryComponents'));
    }

    public function create()
    {
        // Fetch related data for dropdowns
        $companies = Company::all();
        $branches = Branch::all();
        $departments = Department::all();
        $designations = Designation::all();
        $employmentTypes = EmploymentType::all();
        $districts = District::all();

        return view('admin.employees.form', compact(
            'companies',
            'branches',
            'departments',
            'designations',
            'employmentTypes',
            'districts'
        ));
    }

    public function store(Request $request)
    {
        // Generate unique employee code
        do {
            $employee_code = 'EMP' . now()->format('Ymd') . rand(1000, 9999);
        } while (Employee::where('employee_code', $employee_code)->exists());

        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'employment_type' => 'required|exists:employment_type,id',
            'district' => 'required|exists:districts,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:1,2',
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'nullable|date',
            'personal_email' => 'required|email|max:255',
            'official_email' => 'nullable|email|max:255',
            'mobile' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'permanent_address' => 'nullable|string',
            'present_address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nid_no' => 'nullable|string|max:50',
            'passport_no' => 'nullable|string|max:50',
            'joining_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'resume' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        // Create the user first
        $user = User::create([
            'name' => $request->full_name,
            'email' => $request->personal_email,
            'password' => Hash::make($request->password),
            'is_admin' => 0
        ]);

        // Prepare employee data
        $data = $request->except(['password', 'password_confirmation']);
        $data['employee_code'] = $employee_code;
        $data['created_by'] = Auth::id();
        $data['user_id'] = $user->id;

        // Upload files into employee folder
        foreach (['photo', 'joining_letter', 'resume', 'other_documents', 'security_deposit_file'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $data[$fileField] = $request->file($fileField)
                    ->store("employees/$employee_code", 'public');
            }
        }

        // Create the employee first without ledger_id
        $employee = Employee::create($data);

        // Create a ledger for the employee
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
        $employee->update([
            'ledger_id' => $ledger->id
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }

    public function print(Employee $employee)
    {
        $pdf = Pdf::loadView('admin.employees.pdf', compact('employee'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('employee_' . $employee->employee_code . '.pdf');
    }


    public function edit(Employee $employee)
    {
        $companies = Company::all();
        $branches = Branch::all();
        $departments = Department::all();
        $designations = Designation::all();
        $employmentTypes = EmploymentType::all();
        $districts = District::all();

        return view('admin.employees.form', compact(
            'employee',
            'companies',
            'branches',
            'departments',
            'designations',
            'employmentTypes',
            'districts'
        ));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'employment_type' => 'required|exists:employment_type,id',
            'district' => 'required|exists:districts,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:1,2',
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'nullable|date',
            'personal_email' => 'required|email|max:255',
            'official_email' => 'nullable|email|max:255',
            'mobile' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'permanent_address' => 'nullable|string',
            'present_address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nid_no' => 'nullable|string|max:50',
            'passport_no' => 'nullable|string|max:50',
            'joining_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'resume' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $data = $request->all();
        // Upload files into employee folder
        foreach (
            [
                'photo',
                'joining_letter',
                'resume',
                'other_documents',
                'security_deposit_file'
            ] as $fileField
        ) {

            if ($request->hasFile($fileField)) {

                // delete old file
                if ($employee->$fileField && Storage::disk('public')->exists($employee->$fileField)) {
                    Storage::disk('public')->delete($employee->$fileField);
                }

                // store new file in the same folder
                $data[$fileField] = $request->file($fileField)
                    ->store("employees/{$employee->employee_code}", 'public');
            }
        }

        $employee->update($data);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    public function getBranches(Company $company)
    {
        $branches = $company->branches()->where('status', 1)->get();
        return response()->json($branches);
    }
}
