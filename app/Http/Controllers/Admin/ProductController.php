<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductGeneric;
use App\Models\ProductCompany;
use App\Models\Unit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        $units = Unit::where('status', true)->get();

        return view('admin.products.index', compact('products', 'units'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $brands = Brand::where('status', 1)->get();
        $generics = ProductGeneric::all();
        $companies = ProductCompany::all();
        // barcode optional now
        $barcode = rand(100000000000, 999999999999);

        return view('admin.products.form', compact(
            'categories',
            'brands',
            'generics',
            'companies',
            'barcode'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'company_id' => 'nullable|exists:product_companies,id',
            'generic_id' => 'nullable|exists:product_generics,id',
            'name' => 'required|string|max:255',
            'strength' => 'nullable|string|max:100',
            'manufacturer_name' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:0,1',
        ]);

        $data = $request->only([
            'barcode',
            'category_id',
            'brand_id',
            'company_id',
            'generic_id',
            'name',
            'strength',
            'manufacturer_name',
            'price',
            'status',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 1)->get();
        $brands = Brand::where('status', 1)->get();
        $generics = ProductGeneric::all();
        $companies = ProductCompany::all();

        return view('admin.products.form', compact(
            'product',
            'categories',
            'brands',
            'generics',
            'companies'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'company_id' => 'nullable|exists:product_companies,id',
            'generic_id' => 'nullable|exists:product_generics,id',
            'name' => 'required|string|max:255',
            'strength' => 'nullable|string|max:100',
            'manufacturer_name' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:0,1',
        ]);

        $data = $request->only([
            'barcode',
            'category_id',
            'brand_id',
            'company_id',
            'generic_id',
            'name',
            'strength',
            'manufacturer_name',
            'price',
            'status',
        ]);

        if ($request->hasFile('image')) {

            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image && \Storage::disk('public')->exists($product->image)) {
            \Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    // ================= IMPORT =================

    public function import()
    {
        return view('admin.products.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        $file = $request->file('file');

        $headings = (new HeadingRowImport)->toArray($file)[0][0];

        $expectedHeadings = [
            'barcode',
            'category_id',
            'brand_id',
            'company_id',
            'generic_id',
            'name',
            'strength',
            'manufacturer_name',
            'price',
            'status'
        ];

        if ($headings !== $expectedHeadings) {
            return back()->withErrors(['file' => 'Invalid file format']);
        }

        $rows = Excel::toArray([], $file)[0];
        unset($rows[0]);

        $errors = [];

        foreach ($rows as $key => $row) {
            try {

                // ✅ Auto barcode generate if empty
                $barcode = $row[0] ?? null;

                if (empty($barcode)) {
                    do {
                        $barcode = rand(100000000000, 999999999999); // 12 digit
                    } while (Product::where('barcode', $barcode)->exists());
                }

                $data = [
                    'barcode' => $barcode,
                    'category_id' => $row[1] ?? null,
                    'brand_id' => $row[2] ?? null,
                    'company_id' => $row[3] ?? null,
                    'generic_id' => $row[4] ?? null,
                    'name' => $row[5] ?? null,
                    'strength' => $row[6] ?? null,
                    'manufacturer_name' => $row[7] ?? null,
                    'price' => $row[8] ?? 0,
                    'status' => $row[9] ?? 1,
                ];

                $validator = \Validator::make($data, [
                    'barcode' => 'nullable|string|max:50|unique:products,barcode',
                    'category_id' => 'nullable|exists:categories,id',
                    'brand_id' => 'nullable|exists:brands,id',
                    'company_id' => 'nullable|exists:product_companies,id',
                    'generic_id' => 'nullable|exists:product_generics,id',
                    'name' => 'required|string|max:255',
                    'price' => 'required|numeric|min:0',
                    'status' => 'required|in:0,1',
                ]);

                if ($validator->fails()) {
                    $errors[$key + 2] = $validator->errors()->all();
                    continue;
                }

                Product::create($data);
            } catch (\Exception $e) {
                $errors[$key + 2] = [$e->getMessage()];
            }
        }

        if (!empty($errors)) {
            return back()->withErrors(['file' => json_encode($errors)]);
        }

        return redirect()->route('products.index')->with('success', 'Import successful');
    }

    public function sampleDownload()
    {
        $columns = [
            'barcode',
            'category_id',
            'brand_id',
            'company_id',
            'generic_id',
            'name',
            'strength',
            'manufacturer_name',
            'price',
            'status'
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            fputcsv($file, ['123456', '1', '1', '1', '1', 'Paracetamol', '500mg', 'Acme', '10', '1']);

            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=products_sample.csv',
        ]);
    }

    // ================= UNIT =================

    public function setUnit(Request $request, Product $product)
    {
        $rules = [
            'base_unit_id' => ['required', 'exists:units,id', 'different:secondary_unit_id'],
            'secondary_unit_id' => ['nullable', 'exists:units,id', 'different:base_unit_id'],
            'conversion_rate' => ['nullable', 'numeric', 'min:0.01'],
        ];

        if ($request->filled('secondary_unit_id')) {
            $rules['conversion_rate'][] = 'required';
        }

        $validated = $request->validate($rules);

        $product->update([
            'base_unit_id' => $validated['base_unit_id'],
            'secondary_unit_id' => $validated['secondary_unit_id'] ?? null,
            'conversion_rate' => $validated['conversion_rate'] ?? null,
        ]);

        return back()->with('success', 'Unit updated successfully');
    }
}
