<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductGeneric;
use App\Models\ProductCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductRequestController extends Controller
{
    // ================= CREATE FORM =================
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $brands = Brand::where('status', 1)->get();
        $generics = ProductGeneric::all();
        $companies = ProductCompany::all();

        // optional barcode like admin system
        $barcode = rand(100000000000, 999999999999);

        return view('frontend.product.form', compact(
            'categories',
            'brands',
            'generics',
            'companies',
            'barcode'
        ));
    }

    // ================= STORE REQUEST =================
    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'category_id' => 'required|exists:categories,id',
            'company_id' => 'nullable|exists:product_companies,id',
            'generic_id' => 'nullable|exists:product_generics,id',

            // নাম unique করলাম নিচে
            'name' => 'nullable|string|max:255',

            'strength' => 'nullable|string|max:100',
            'manufacturer_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'barcode' => $request->barcode,
            'category_id' => $request->category_id,
            'company_id' => $request->company_id,
            'generic_id' => $request->generic_id,
            'strength' => $request->strength,
            'manufacturer_name' => $request->manufacturer_name,
            'description' => $request->description,
            'status' => 0,
        ];

        // image upload + auto name
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $path = $file->store('products', 'public');
            $data['image'] = $path;

            // যদি name না থাকে তাহলে image থেকে বানাবে
            if (empty($request->name)) {

                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $cleanName = Str::of($filename)
                    ->replace(['_', '-'], ' ')
                    ->squish()
                    ->title();

                $data['name'] = $cleanName;
            }
        }

        // যদি image না থাকে কিন্তু name থাকে
        if (!empty($request->name)) {
            $data['name'] = Str::of($request->name)->squish()->title();
        }

        // 🔥 duplicate name check (approved + pending দুটোতেই না হয়)
        $exists = Product::where('name', $data['name'])->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['name' => 'This product name already exists.']);
        }

        Product::create($data);

        return redirect()->back()
            ->with('success', 'Your product request has been submitted and is pending approval.');
    }
}
