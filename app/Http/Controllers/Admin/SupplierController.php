<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    // List all suppliers with pagination
    public function index()
    {
        $suppliers = Supplier::orderBy('id', 'desc')->paginate(10);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    // Show form to create a new supplier
    public function create()
    {
        return view('admin.suppliers.form'); // unified form
    }

    // Store new supplier
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'mobile'                => 'required|string|max:20',
            'email'                 => 'nullable|email',
            'address'               => 'nullable|string',
            'contact_person'        => 'nullable|string|max:255',
            'contact_person_mobile' => 'nullable|string|max:20',
            'status'                => 'required|in:1,0',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id(); // if column exists

        Supplier::create($data);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    // Show form to edit a supplier
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.form', compact('supplier')); // unified form
    }

    // Update supplier
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'mobile'                => 'required|string|max:20',
            'email'                 => 'nullable|email',
            'address'               => 'nullable|string',
            'contact_person'        => 'nullable|string|max:255',
            'contact_person_mobile' => 'nullable|string|max:20',
            'status'                => 'required|in:1,0',
        ]);

        $supplier->update($request->all());

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    // Delete supplier
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
