<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\District;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    // List all customers with pagination
    public function index(Request $request)
    {
        $query = Customer::query();

        // status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $customers = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.customers.index', compact('customers'));
    }

    // Show form to create a new customer
    public function create()
    {
        $districts = District::all();

        return view('admin.customers.form', compact('districts'));
    }

    // Store new customer
    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'email'              => 'nullable|email',
            'address'            => 'nullable|string',
            'emergency_contact'  => 'nullable|string|max:20',
            'district_id'        => 'nullable|integer|exists:districts,id',
            'postcode'           => 'nullable|string|max:20',
            'status'             => 'required|in:1,0',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id(); // if column exists

        Customer::create($data);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    // Show form to edit a customer
    public function edit(Customer $customer)
    {
        $districts = District::all();

        return view('admin.customers.form', compact('customer', 'districts'));
    }

    // Update customer
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'email'              => 'nullable|email',
            'address'            => 'nullable|string',
            'emergency_contact'  => 'nullable|string|max:20',
            'district_id'        => 'nullable|integer|exists:districts,id',
            'postcode'           => 'nullable|string|max:20',
            'status'             => 'required|in:1,0',
        ]);

        $customer->update($request->all());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    // Delete customer
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
