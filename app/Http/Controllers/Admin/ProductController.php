<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductGeneric;
use App\Models\ProductCompany;
use App\Models\Unit;
use App\Models\UserType;
use App\Models\ProductCommission;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;


class ProductController extends Controller
{


    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = Product::with([
                'category',
                'generic',
                'prices.unit',
                'commissions', // ✅ IMPORTANT ADD
                'company'
            ])
                ->select('products.*')
                ->where('status', 1);

            return DataTables::of($query)
                ->addIndexColumn()

                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset('public/storage/' . $row->image) . '" width="180" height="130" class="rounded">';
                    }
                    return '<span class="text-muted">No Image</span>';
                })

                ->addColumn('category', function ($row) {
                    return $row->category->name ?? '-';
                })

                ->addColumn('strength', function ($row) {
                    return $row->strength ?? '-';
                })

                ->addColumn('generic', function ($row) {
                    return $row->generic->generic_name ?? '-';
                })

                ->filterColumn('generic', function ($query, $keyword) {
                    $query->whereHas('generic', function ($q) use ($keyword) {
                        $q->where('generic_name', 'like', "%{$keyword}%");
                    });
                })

                ->addColumn('company', function ($row) {
                    return $row->company->company_name ?? '-';
                })

                ->filterColumn('company', function ($query, $keyword) {
                    $query->whereHas('company', function ($q) use ($keyword) {
                        $q->where('company_name', 'like', "%{$keyword}%");
                    });
                })

                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })

                ->addColumn('action', function ($row) {

                    return '
                <button class="btn btn-sm btn-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#setUnitModal"
                    data-id="' . $row->id . '"
                    data-name="' . $row->name . '"
                    data-url="' . route('products.unit.set', $row->id) . '"
                    data-base="' . ($row->base_unit_id ?? '') . '"
                    data-secondary="' . ($row->secondary_unit_id ?? '') . '"
                    data-rate="' . ($row->conversion_rate ?? '') . '">
                    Set Unit
                </button>

                <button class="btn btn-sm btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#setPriceModal"
                    data-id="' . $row->id . '"
                    data-name="' . $row->name . '"
                    data-url="' . route('products.price.set', $row->id) . '"
                    data-base-unit="' . ($row->base_unit_id ?? 0) . '"
                    data-secondary-unit="' . ($row->secondary_unit_id ?? 0) . '"
                    data-prices="' . htmlspecialchars(json_encode($row->prices ?? []), ENT_QUOTES, 'UTF-8') . '">
                    Set Price
                </button>

                <button class="btn btn-sm btn-info"
                    data-bs-toggle="modal"
                    data-bs-target="#setCommissionModal"
                    data-id="' . $row->id . '"
                    data-name="' . $row->name . '"
                    data-url="' . route('products.commission.set', $row->id) . '"
                    data-commissions="' . htmlspecialchars(json_encode($row->commissions ?? []), ENT_QUOTES, 'UTF-8') . '">
                    Commission
                </button>

                <a href="' . route('products.edit', $row->id) . '" class="btn btn-sm btn-primary">
                    Edit
                </a>

                <form method="POST"
                    action="' . route('products.destroy', $row->id) . '"
                    style="display:inline-block"
                    onsubmit="return confirm(\'Delete this product?\')">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>';
                })

                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        $units = Unit::where('status', true)->get();
        $userTypes = UserType::where('status', 1)
            ->whereNotIn('id', [USER_TYPE_SUPPLIER])
            ->get();

        return view('admin.products.index', compact('units', 'userTypes'));
    }

    public function productRequest(Request $request)
    {
        if ($request->ajax()) {

            $query = Product::with([
                'category',
                'generic',
                'prices',
                'commissions',
                'company'
            ])
                ->select('products.*')
                ->where('status', 0)
                ->latest();

            return DataTables::of($query)
                ->addIndexColumn()

                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset('public/storage/' . $row->image) . '" width="180" height="130" class="rounded">';
                    }

                    return '<span class="text-muted">No Image</span>';
                })

                ->addColumn('category', function ($row) {
                    return $row->category->name ?? '-';
                })

                ->addColumn('strength', function ($row) {
                    return $row->strength ?? '-';
                })

                ->addColumn('generic', function ($row) {
                    return $row->generic->generic_name ?? '-';
                })

                ->filterColumn('generic', function ($query, $keyword) {
                    $query->whereHas('generic', function ($q) use ($keyword) {
                        $q->where('generic_name', 'like', "%{$keyword}%");
                    });
                })

                ->addColumn('company', function ($row) {
                    return $row->company?->company_name ?? '-';
                })

                ->filterColumn('company', function ($query, $keyword) {
                    $query->whereHas('company', function ($q) use ($keyword) {
                        $q->where('company_name', 'like', "%{$keyword}%");
                    });
                })

                ->addColumn('status', function ($row) {
                    return '<span class="badge bg-warning">Pending</span>';
                })

                ->addColumn('action', function ($row) {

                    return '
                <button class="btn btn-sm btn-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#setUnitModal"
                    data-id="' . $row->id . '"
                    data-name="' . $row->name . '"
                    data-url="' . route('products.unit.set', $row->id) . '"
                    data-base="' . ($row->base_unit_id ?? '') . '"
                    data-secondary="' . ($row->secondary_unit_id ?? '') . '"
                    data-rate="' . ($row->conversion_rate ?? '') . '">
                    Set Unit
                </button>

                <button class="btn btn-sm btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#setPriceModal"
                    data-id="' . $row->id . '"
                    data-name="' . $row->name . '"
                    data-url="' . route('products.price.set', $row->id) . '"
                    data-prices="' . htmlspecialchars(json_encode($row->prices ?? []), ENT_QUOTES, 'UTF-8') . '">
                    Set Price
                </button>

                <button class="btn btn-sm btn-info"
                    data-bs-toggle="modal"
                    data-bs-target="#setCommissionModal"
                    data-id="' . $row->id . '"
                    data-name="' . $row->name . '"
                    data-url="' . route('products.commission.set', $row->id) . '"
                    data-commissions="' . htmlspecialchars(json_encode($row->commissions ?? []), ENT_QUOTES, 'UTF-8') . '">
                    Commission
                </button>

                <a href="' . route('products.edit', $row->id) . '?from=request"
                    class="btn btn-sm btn-primary">
                    Edit
                </a>

                <form method="POST"
                    action="' . route('products.destroy', $row->id) . '"
                    style="display:inline-block"
                    onsubmit="return confirm(\'Delete this product?\')">

                    ' . csrf_field() . method_field('DELETE') . '

                    <button class="btn btn-sm btn-danger">
                        Delete
                    </button>
                </form>';
                })

                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        $units = Unit::where('status', true)->get();
        $userTypes = UserType::where('status', 1)
            ->whereNotIn('id', [USER_TYPE_SUPPLIER])
            ->get();

        return view('admin.products.request', compact('units', 'userTypes'));
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

            // Name unique করা হলো
            'name' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'name'),
            ],

            'image' => 'nullable|image|max:2048|required_without:name',

            'strength' => 'nullable|string|max:100',
            'manufacturer_name' => 'nullable|string|max:255',
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
            'status',
        ]);

        /**
         * IMAGE UPLOAD + AUTO NAME
         */
        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $data['image'] = $file->store('products', 'public');

            // যদি name না দেওয়া হয় তাহলে image থেকে বানাবে
            if (empty($request->name)) {

                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $cleanName = Str::of($filename)
                    ->replace(['_', '-'], ' ')
                    ->squish()
                    ->title();

                $data['name'] = $cleanName;
            }
        }

        /**
         * যদি user name দেয় তাহলে clean করা
         */
        if (!empty($request->name)) {
            $data['name'] = Str::of($request->name)
                ->squish()
                ->title();
        }

        /**
         * EXTRA SAFETY: Duplicate Check (Optional but Strong)
         */
        if (Product::where('name', $data['name'])->exists()) {
            return back()
                ->withErrors(['name' => 'This product name already exists.'])
                ->withInput();
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
            'status',
        ]);

        /**
         * Clean Name
         */
        $data['name'] = Str::of($request->name)
            ->squish()
            ->title();

        /**
         * Duplicate Check
         * Same Name + Same Strength not allowed
         */
        $exists = Product::where('name', $data['name'])
            ->where('strength', $request->strength)
            ->where('id', '!=', $product->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'name' => 'This product name and strength already exists.'
                ])
                ->withInput();
        }

        /**
         * Image Update
         */
        if ($request->hasFile('image')) {

            if (
                $product->image &&
                \Storage::disk('public')->exists($product->image)
            ) {
                \Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')
                ->store('products', 'public');
        }

        $product->update($data);

        /**
         * Redirect Back To Previous Page
         */
        if ($request->has('from') && $request->from === 'request') {
            return redirect()
                ->route('products.request')
                ->with('success', 'Product updated successfully.');
        }

        return redirect()
            ->route('products.index')
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

        // normalize headings
        $headings = array_map(fn($h) => strtolower(trim($h)), $headings);

        if ($headings !== $expectedHeadings) {
            return back()->withErrors(['file' => 'Invalid file format']);
        }

        $rows = Excel::toArray([], $file)[0];
        unset($rows[0]);

        $errors = [];

        foreach ($rows as $key => $row) {
            try {

                // ✅ Clean values
                $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

                // ✅ Barcode
                $barcode = $row[0] ?? null;

                if (empty($barcode)) {
                    do {
                        $barcode = rand(100000000000, 999999999999);
                    } while (Product::where('barcode', $barcode)->exists());
                }

                // ✅ Safe foreign key resolver
                $category_id = (!empty($row[1]) && \DB::table('categories')->where('id', (int)$row[1])->exists())
                    ? (int)$row[1] : null;

                $brand_id = (!empty($row[2]) && \DB::table('brands')->where('id', (int)$row[2])->exists())
                    ? (int)$row[2] : null;

                $company_id = (!empty($row[3]) && \DB::table('product_companies')->where('id', (int)$row[3])->exists())
                    ? (int)$row[3] : null;

                $generic_id = (!empty($row[4]) && \DB::table('product_generics')->where('id', (int)$row[4])->exists())
                    ? (int)$row[4] : null;

                // ✅ Price safe
                $price = is_numeric($row[8]) ? $row[8] : 0;

                $data = [
                    'barcode' => $barcode,
                    'category_id' => $category_id,
                    'brand_id' => $brand_id,
                    'company_id' => $company_id,
                    'generic_id' => $generic_id,
                    'name' => $row[5] ?? null,
                    'strength' => $row[6] ?? null,
                    'manufacturer_name' => $row[7] ?? null,
                    'price' => $price,
                    'status' => isset($row[9]) ? (int)$row[9] : 1,
                ];

                // ✅ Keep only important validation
                $validator = \Validator::make($data, [
                    'barcode' => 'nullable|string|max:50|unique:products,barcode',
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

    public function setPrice(Request $request, Product $product)
    {
        $request->validate([
            'user_type_id' => ['required', 'array'],
            'user_type_id.*' => ['exists:user_type,id'],

            'unit_id' => ['required', 'array'],
            'unit_id.*' => ['exists:units,id'],

            'price' => ['required', 'array'],
            'price.*' => ['nullable', 'numeric', 'min:0'],

            'currency' => ['required', 'in:1,2,3'],
        ]);

        // 🔥 STEP 1: DELETE OLD PRICES (IMPORTANT)
        \App\Models\ProductPrice::where('product_id', $product->id)->delete();

        // 🔥 STEP 2: INSERT NEW PRICES
        foreach ($request->user_type_id as $index => $userTypeId) {

            $unitId = $request->unit_id[$index] ?? null;
            $price  = $request->price[$index] ?? null;

            if (!$unitId || $price === null || $price === '') {
                continue;
            }

            \App\Models\ProductPrice::create([
                'product_id'    => $product->id,
                'user_type_id'  => $userTypeId,
                'unit_id'       => $unitId,
                'price'         => $price,
                'currency'      => $request->currency,
                'status'        => 1,
            ]);
        }

        return back()->with('success', 'Prices updated successfully');
    }

    public function setCommission(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        foreach ($request->user_type_id as $key => $userTypeId) {

            ProductCommission::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'user_type_id' => $userTypeId,
                ],
                [
                    'commission' => $request->commission[$key] ?? 0,

                    // ⚠️ IMPORTANT: per-user-type commission type
                    'commission_type' => $request->commission_type[$key] ?? COMMISSION_TYPE_FLAT,

                    'status' => 1,
                ]
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Commission updated successfully');
    }
}
