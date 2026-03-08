<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of brands.
     */
    public function index()
    {
        // Paginate brands to 10 per page, latest first
        $brands = Brand::latest()->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('admin.brands.form');
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100|unique:brands,name',
            'status' => 'required|in:0,1',
        ]);

        Brand::create($request->all());

        return redirect()->route('brands.index')
            ->with('success', 'Brand created successfully.');
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.form', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name'   => 'required|string|max:100|unique:brands,name,' . $brand->id,
            'status' => 'required|in:0,1',
        ]);

        $brand->update($request->all());

        return redirect()->route('brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('brands.index')
            ->with('success', 'Brand deleted successfully.');
    }
}