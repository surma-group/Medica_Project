<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of units.
     */
    public function index()
    {
        // Paginate units, latest first
        $units = Unit::latest()->paginate(10);

        return view('admin.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new unit.
     */
    public function create()
    {
        return view('admin.units.form');
    }

    /**
     * Store a newly created unit in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'  => 'required|string|max:100|unique:units,title',
            'status' => 'required|in:0,1',
        ]);

        Unit::create($request->all());

        return redirect()->route('unit.index')
            ->with('success', 'Unit created successfully.');
    }

    /**
     * Show the form for editing the specified unit.
     */
    public function edit(Unit $unit)
    {
        return view('admin.units.form', compact('unit'));
    }

    /**
     * Update the specified unit in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'title'  => 'required|string|max:100|unique:units,title,' . $unit->id,
            'status' => 'required|in:0,1',
        ]);

        $unit->update($request->all());

        return redirect()->route('unit.index')
            ->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified unit from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('unit.index')
            ->with('success', 'Unit deleted successfully.');
    }
}