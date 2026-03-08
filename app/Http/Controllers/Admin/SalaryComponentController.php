<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    public function index()
    {
        $components = SalaryComponent::all();
        return view('admin.salary_components.index', compact('components'));
    }

    public function create()
    {
        return view('admin.salary_components.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'payment_type' => 'required|in:1,2',
            'amount_type' => 'required|in:1,2',
            'status' => 'required|in:0,1',
        ]);

        SalaryComponent::create($request->all());

        return redirect()->route('salary_components.index')
            ->with('success', 'Salary Component created successfully.');
    }

    public function edit(SalaryComponent $salaryComponent)
    {
        return view('admin.salary_components.form', compact('salaryComponent'));
    }

    public function update(Request $request, SalaryComponent $salaryComponent)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'payment_type' => 'required|in:1,2',
            'amount_type' => 'required|in:1,2',
            'status' => 'required|in:0,1',
        ]);

        $salaryComponent->update($request->all());

        return redirect()->route('salary_components.index')
            ->with('success', 'Salary Component updated successfully.');
    }

    public function destroy(SalaryComponent $salaryComponent)
    {
        $salaryComponent->delete();

        return redirect()->route('salary_components.index')
            ->with('success', 'Salary Component deleted successfully.');
    }
}
