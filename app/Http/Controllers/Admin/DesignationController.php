<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::latest()->paginate(10);
        return view('admin.designations.index', compact('designations'));
    }

    public function create()
    {
        return view('admin.designations.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:designations,code',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        Designation::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('designation.index')
            ->with('success', 'Designation created successfully.');
    }

    public function edit(Designation $designation)
    {
        return view('admin.designations.form', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:designations,code,' . $designation->id,
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        $designation->update($request->all());

        return redirect()->route('designation.index')
            ->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();
        return redirect()->route('designation.index')
            ->with('success', 'Designation deleted successfully.');
    }
}
