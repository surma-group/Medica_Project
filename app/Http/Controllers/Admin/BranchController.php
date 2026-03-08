<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\District;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of branches.
     */
   public function index()
    {
        // Eager load company and districtInfo to show names in the list efficiently
        $branches = Branch::with(['company', 'districtInfo'])->latest()->paginate(10);

        return view('admin.branches.index', compact('branches'));
    }


    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        $companies = Company::all();
        $districts = District::all();

        return view('admin.branches.form', compact('companies', 'districts'));
    }

    /**
     * Store a newly created branch in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_code' => 'required|string|max:255|unique:branches,branch_code',
            'branch_name' => 'required|string|max:255',
            'is_head_office' => 'required|in:0,1',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'district' => 'required|exists:districts,id',
            'opening_date' => 'nullable|date',
            'status' => 'required|in:0,1',
        ]);

        Branch::create($request->all());

        return redirect()->route('branch.index')
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch)
    {
        $companies = Company::all();
        $districts = District::all();

        return view('admin.branches.form', compact('branch', 'companies', 'districts'));
    }

    /**
     * Update the specified branch in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_code' => 'required|string|max:255|unique:branches,branch_code,' . $branch->id,
            'branch_name' => 'required|string|max:255',
            'is_head_office' => 'required|in:0,1',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'district' => 'required|exists:districts,id',
            'opening_date' => 'nullable|date',
            'status' => 'required|in:0,1',
        ]);

        $branch->update($request->all());

        return redirect()->route('branch.index')
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified branch from storage.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('branch.index')
            ->with('success', 'Branch deleted successfully.');
    }
}
