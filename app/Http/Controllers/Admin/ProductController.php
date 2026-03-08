<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        // Paginate products, latest first
        $products = Product::latest()->paginate(10);
        $units = Unit::where('status', true)->get();
        return view('admin.products.index', compact('products', 'units'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $brands = Brand::where('status', 1)->get();

        // Auto generate unique barcode
        do {
            $barcode = rand(100000000000, 999999999999); // 12 digit random
        } while (\App\Models\Product::where('barcode', $barcode)->exists());

        return view('admin.products.form', compact('categories', 'brands', 'barcode'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string|max:50|unique:products,barcode',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'strength' => 'nullable|string|max:100',
            'manufacturer_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:0,1',
        ]);

        $data = $request->only([
            'barcode',
            'category_id',
            'brand_id',
            'name',
            'generic_name',
            'strength',
            'manufacturer_name',
            'status',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('status', 1)->get();
        $brands = Brand::where('status', 1)->get();
        return view('admin.products.form', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'barcode' => 'required|string|max:50|unique:products,barcode,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'strength' => 'nullable|string|max:100',
            'manufacturer_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:0,1',
        ]);

        $data = $request->only([
            'barcode',
            'category_id',
            'brand_id',
            'name',
            'generic_name',
            'strength',
            'manufacturer_name',
            'status',
        ]);

        if ($request->hasFile('image')) {

            // Delete old image
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image if exists
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

    public function import()
    {
        return view('admin.products.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv',
        ]);

        $file = $request->file('file');

        // Get headings to validate format (optional)
        $headings = (new HeadingRowImport)->toArray($file)[0][0];

        $expectedHeadings = ['barcode', 'category_id', 'brand_id', 'name', 'generic_name', 'strength', 'manufacturer_name', 'status'];

        if ($headings !== $expectedHeadings) {
            return redirect()->back()->withErrors(['file' => 'Excel file headings are incorrect. Please download the sample file.']);
        }

        // Read Excel file
        $rows = Excel::toArray([], $file)[0]; // First sheet

        // Skip header row
        unset($rows[0]);

        $errors = [];
        foreach ($rows as $key => $row) {
            try {
                // Validate each row
                $data = [
                    'barcode' => (string) ($row[0] ?? ''),
                    'category_id' => $row[1] ?? null,
                    'brand_id' => $row[2] ?? null,
                    'name' => $row[3] ?? null,
                    'generic_name' => $row[4] ?? null,
                    'strength' => $row[5] ?? null,
                    'manufacturer_name' => $row[6] ?? null,
                    'status' => $row[7] ?? 1,
                ];

                // You can use Validator if needed
                $validator = \Validator::make($data, [
                    'barcode' => 'required|string|max:50|unique:products,barcode',
                    'category_id' => 'required|exists:categories,id',
                    'brand_id' => 'required|exists:brands,id',
                    'name' => 'required|string|max:255',
                    'generic_name' => 'nullable|string|max:255',
                    'strength' => 'nullable|string|max:100',
                    'manufacturer_name' => 'nullable|string|max:255',
                    'status' => 'required|in:0,1',
                ]);

                if ($validator->fails()) {
                    $errors[$key + 2] = $validator->errors()->all(); // +2 because Excel rows start at 1 + header
                    continue;
                }

                Product::create($data);
            } catch (\Exception $e) {
                $errors[$key + 2] = [$e->getMessage()];
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors(['file' => 'Some rows failed to import: ' . json_encode($errors)]);
        }

        return redirect()->route('products.index')->with('success', 'Products imported successfully!');
    }

    public function sampleDownload()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_sample.csv"',
        ];

        $columns = ['barcode', 'category_id', 'brand_id', 'name', 'generic_name', 'strength', 'manufacturer_name', 'status'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // header row
            // Example rows
            fputcsv($file, ['17890123', '1', '1', 'Paracetamol', 'Acetaminophen', '500mg', 'Acme Pharma', '1']);
            fputcsv($file, ['93210987', '2', '3', 'Amoxicillin', 'Amoxil', '250mg', 'Global Pharma', '1']);
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Set Base Unit, Secondary Unit, and Conversion Rate for a product.
     */
    /**
     * Set Base Unit, Secondary Unit, and Conversion Rate for a product.
     */
    public function setUnit(Request $request, Product $product)
    {
        // Custom validation rules
        $rules = [
            'base_unit_id' => ['required', 'exists:units,id', 'different:secondary_unit_id'],
            'secondary_unit_id' => ['nullable', 'exists:units,id', 'different:base_unit_id'],
            // conversion_rate is required only if secondary_unit_id is present
            'conversion_rate' => ['nullable', 'numeric', 'min:0.01'],
        ];

        // Conditional: if secondary_unit_id is selected, conversion_rate becomes required
        if ($request->filled('secondary_unit_id')) {
            $rules['conversion_rate'][] = 'required';
        }

        // Validate the request
        $validated = $request->validate($rules);

        // Update product unit fields
        $product->update([
            'base_unit_id' => $validated['base_unit_id'],
            'secondary_unit_id' => $validated['secondary_unit_id'] ?? null,
            'conversion_rate' => $validated['conversion_rate'] ?? null,
        ]);

        return redirect()->back()->with('success', "Units for '{$product->name}' updated successfully.");
    }
}
