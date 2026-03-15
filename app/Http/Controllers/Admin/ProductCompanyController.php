<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCompany;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Response;

class ProductCompanyController extends Controller
{

    /**
     * Display a listing
     */
    public function index()
    {
        $companies = ProductCompany::orderBy('company_order')
            ->paginate(10);

        return view('admin.product_company.index', compact('companies'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.product_company.form');
    }

    /**
     * Store new company
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_order' => 'nullable|integer',
            'status' => 'required|in:0,1'
        ]);

        ProductCompany::create($request->only(
            'company_name',
            'company_order',
            'status'
        ));

        return redirect()->route('product_company.index')
            ->with('success', 'Company created successfully.');
    }

    /**
     * Edit form
     */
    public function edit(ProductCompany $product_company)
    {
        return view('admin.product_company.form', compact('product_company'));
    }

    /**
     * Update
     */
    public function update(Request $request, ProductCompany $product_company)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_order' => 'nullable|integer',
            'status' => 'required|in:0,1'
        ]);

        $product_company->update($request->only(
            'company_name',
            'company_order',
            'status'
        ));

        return redirect()->route('product_company.index')
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Delete
     */
    public function destroy(ProductCompany $product_company)
    {
        $product_company->delete();

        return redirect()->route('product_company.index')
            ->with('success', 'Company deleted successfully.');
    }


    /**
     * Import Page
     */
    public function import()
    {
        return view('admin.product_company.import');
    }


    /**
     * Import CSV / Excel
     */
    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv',
        ]);

        $file = $request->file('file');

        $headings = (new \Maatwebsite\Excel\HeadingRowImport)->toArray($file)[0][0];

        $expectedHeadings = [
            'id',
            'company_name',
            'company_order'
        ];

        if ($headings !== $expectedHeadings) {
            return back()->withErrors([
                'file' => 'Invalid file format. Please download the sample file.'
            ]);
        }

        $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0];

        unset($rows[0]); // remove header

        $errors = [];

        foreach ($rows as $key => $row) {

            try {

                $data = [
                    'id'            => $row[0] ?? null,
                    'company_name'  => $row[1] ?? null,
                    'company_order' => $row[2] ?? 0,
                    'status'        => 1, // default
                ];

                $validator = \Validator::make($data, [
                    'id'            => 'nullable|integer',
                    'company_name'  => 'required|string|max:255',
                    'company_order' => 'nullable|integer',
                ]);

                if ($validator->fails()) {
                    $errors[$key + 2] = $validator->errors()->all();
                    continue;
                }

                if (!empty($data['id'])) {

                    // If ID exists → update
                    ProductCompany::updateOrCreate(
                        ['id' => $data['id']],
                        [
                            'company_name'  => $data['company_name'],
                            'company_order' => $data['company_order'],
                            'status'        => 1,
                        ]
                    );
                } else {

                    // If no ID → create new
                    ProductCompany::create([
                        'company_name'  => $data['company_name'],
                        'company_order' => $data['company_order'],
                        'status'        => 1,
                    ]);
                }
            } catch (\Exception $e) {

                $errors[$key + 2] = [$e->getMessage()];
            }
        }

        if (!empty($errors)) {
            return back()->withErrors([
                'file' => 'Some rows failed: ' . json_encode($errors)
            ]);
        }

        return redirect()->route('product_company.index')
            ->with('success', 'Companies imported successfully');
    }


    /**
     * Download Sample CSV
     */
    public function sampleDownload()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product_company_sample.csv"',
        ];

        $columns = [
            'id',
            'company_name',
            'company_order'
        ];

        $callback = function () use ($columns) {

            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, $columns);

            // Sample data
            fputcsv($file, ['1', 'Square Pharma', '1']);
            fputcsv($file, ['2', 'Beximco Pharma', '2']);

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
