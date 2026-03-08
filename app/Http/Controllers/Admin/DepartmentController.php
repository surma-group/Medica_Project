<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    // List all departments with pagination
    public function index()
    {
        $departments = Department::orderBy('id', 'desc')->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }

    // Show form to create a new department
    public function create()
    {
        return view('admin.departments.form'); // unified form
    }

    // Store new department
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:departments,code',
            'description' => 'nullable|string',
            'status' => 'required|in:1,0',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();

        Department::create($data);

        return redirect()->route('department.index')->with('success', 'Department created successfully.');
    }

    // Show form to edit a department
    public function edit(Department $department)
    {
        return view('admin.departments.form', compact('department')); // unified form
    }

    // Update department
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'status' => 'required|in:1,0',
        ]);

        $department->update($request->all());

        return redirect()->route('department.index')->with('success', 'Department updated successfully.');
    }

    // Delete department
    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('department.index')->with('success', 'Department deleted successfully.');
    }
}
