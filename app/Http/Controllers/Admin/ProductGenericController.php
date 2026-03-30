<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductGeneric;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Response;

class ProductGenericController extends Controller
{

    /**
     * Display a listing
     */
    public function index()
    {
        $generics = ProductGeneric::orderBy('generic_name')
            ->paginate(10);

        return view('admin.product_generic.index', compact('generics'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.product_generic.form');
    }

    /**
     * Store new generic
     */
    public function store(Request $request)
    {
        $request->validate([
            'generic_name' => 'required|string|max:255',
            'status' => 'required|in:0,1'
        ]);

        ProductGeneric::create($request->only(
            'generic_id',
            'generic_name',
            'precaution',
            'indication',
            'contra_indication',
            'dose',
            'side_effect',
            'mode_of_action',
            'interaction',
            'pregnancy_category_id',
            'status'
        ));

        return redirect()->route('product_generic.index')
            ->with('success', 'Generic created successfully.');
    }

    /**
     * Edit form
     */
    public function edit(ProductGeneric $product_generic)
    {
        return view('admin.product_generic.form', compact('product_generic'));
    }

    /**
     * Update
     */
    public function update(Request $request, ProductGeneric $product_generic)
    {
        $request->validate([
            'generic_name' => 'required|string|max:255',
            'status' => 'required|in:0,1'
        ]);

        $product_generic->update($request->only(
            'generic_id',
            'generic_name',
            'precaution',
            'indication',
            'contra_indication',
            'dose',
            'side_effect',
            'mode_of_action',
            'interaction',
            'pregnancy_category_id',
            'status'
        ));

        return redirect()->route('product_generic.index')
            ->with('success', 'Generic updated successfully.');
    }

    /**
     * Delete
     */
    public function destroy(ProductGeneric $product_generic)
    {
        $product_generic->delete();

        return redirect()->route('product_generic.index')
            ->with('success', 'Generic deleted successfully.');
    }


    /**
     * Import Page
     */
    public function import()
    {
        return view('admin.product_generic.import');
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

        $headings = (new HeadingRowImport)->toArray($file)[0][0];

        $expectedHeadings = [
            'id',
            'generic_name'
        ];

        if ($headings !== $expectedHeadings) {
            return back()->withErrors([
                'file' => 'Invalid file format. Please download the sample file.'
            ]);
        }

        $rows = Excel::toArray([], $file)[0];

        unset($rows[0]); // remove header

        $errors = [];

        foreach ($rows as $key => $row) {

            try {

                $data = [
                    'id'           => $row[0] ?? null,
                    'generic_name' => $row[1] ?? null,
                    'status'       => 1
                ];

                $validator = \Validator::make($data, [
                    'id'           => 'nullable|integer',
                    'generic_name' => 'required|string|max:255',
                ]);

                if ($validator->fails()) {
                    $errors[$key + 2] = $validator->errors()->all();
                    continue;
                }

                if (!empty($data['id'])) {

                    ProductGeneric::updateOrCreate(
                        ['id' => $data['id']],
                        [
                            'generic_name' => $data['generic_name'],
                            'status'       => 1
                        ]
                    );

                } else {

                    ProductGeneric::create([
                        'generic_name' => $data['generic_name'],
                        'status'       => 1
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

        return redirect()->route('product_generic.index')
            ->with('success', 'Generics imported successfully');
    }


    /**
     * Download Sample CSV
     */
    public function sampleDownload()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product_generic_sample.csv"',
        ];

        $columns = [
            'id',
            'generic_name'
        ];

        $callback = function () use ($columns) {

            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, $columns);

            // Sample Data
            fputcsv($file, ['1', 'Paracetamol']);
            fputcsv($file, ['2', 'Amoxicillin']);

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}