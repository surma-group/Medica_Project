<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ProductCompany;
use App\Models\Category;
use App\Models\Unit;
use App\Models\UserType;
use App\Models\Customer;
use App\Models\Partner;
use App\Services\StockService;
use App\Services\AccountingService;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input(
            'date_from',
            now()->format('Y-m-d')
        );

        $dateTo = $request->input(
            'date_to',
            now()->format('Y-m-d')
        );

        $query = Sale::with([
            'items',
            'userType'
        ]);

        // Date From
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        // Date To
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Sale No Search
        if ($request->filled('sale_number')) {
            $query->where(
                'sale_number',
                'like',
                '%' . $request->sale_number . '%'
            );
        }

        $sales = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $companies = ProductCompany::where('status', 1)->get();

        $categories = Category::where('status', 1)->get();

        $units = Unit::all();

        $userTypes = UserType::where('status', 1)
            ->where('id', '!=', USER_TYPE_SUPPLIER)
            ->orderBy('name')
            ->get();

        return view('admin.sales.create', compact(
            'companies',
            'categories',
            'units',
            'userTypes'
        ));
    }

    public function store(
        Request $request,
        StockService $stockService,
        AccountingService $accountingService
    ) {

        if ($request->has('items') && is_string($request->items)) {

            $decodedItems = json_decode($request->items, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge([
                    'items' => $decodedItems
                ]);
            }
        }

        $request->validate([
            'sale_date' => 'required|date',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_id' => 'required|exists:units,id',
        ]);

        $subtotal = 0;

        foreach ($request->items as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        $deliveryCharge = 0;
        $discount = 0;

        $grandTotal = ($subtotal + $deliveryCharge) - $discount;

        DB::beginTransaction();

        try {

            $sale = Sale::create([
                'user_type_id' => USER_TYPE_CUSTOMER,
                'reference_id' => $request->customer_id,
                'created_by' => auth()->id(),

                'sale_number' => 'SAL-' . now()->format('YmdHis') . rand(100, 999),

                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'discount' => $discount,
                'grand_total' => $grandTotal,

                'payment_method' => 1,
                'status' => 1,

                'note' => $request->note ?? 'New Sale',
            ]);

            foreach ($request->items as $item) {

                $itemTotal = $item['price'] * $item['quantity'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal,
                    'unit_id' => $item['unit_id'],
                    'status' => 1,
                ]);

                // Stock Reduce
                $stockService->decreaseStock(
                    productId: $item['product_id'],
                    quantity: $item['quantity'],
                    unitId: $item['unit_id'],
                    referenceId: $sale->id,
                    referenceType: 'Sale',
                    note: 'Stock sold from sale #' . $sale->sale_number
                );
            }

            // Accounting Entry
            $accountingService->addTransaction(
                amount: $grandTotal,
                debitLedgerId: L_CASH_DESK,
                creditLedgerId: L_INVENTORY,
                voucherType: VT_SALE,
                referenceId: $sale->id,
                note: 'Sale #' . $sale->sale_number
            );

            DB::commit();

            return redirect()
                ->route('sales.index')
                ->with('success', 'Sale created successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $sale = Sale::with([
            'items.product',
            'items.unit',
            'userType',
            'creator'
        ])->findOrFail($id);

        return view('admin.sales.show', compact('sale'));
    }

    public function companyProducts($companyId, $categoryId = null)
    {
        $query = Product::where('company_id', $companyId)
            ->where('status', 1);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return response()->json(
            $query->select('id', 'name', 'strength')
                ->orderBy('name')
                ->get()
        );
    }

    public function companyCategories($companyId)
    {
        $categories = Category::whereHas('products', function ($q) use ($companyId) {

            $q->where('company_id', $companyId)
                ->where('status', 1);
        })
            ->where('status', 1)
            ->select('id', 'name')
            ->distinct()
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    public function productDetails(Request $request, $productId)
    {
        $product = Product::with([
            'stock',
            'category',
            'baseUnit',
            'secondaryUnit'
        ])->findOrFail($productId);

        $baseUnitId = $product->base_unit_id ?? DEFAULT_UNIT;

        $baseUnit = Unit::find($baseUnitId);

        // Customer Type অনুযায়ী Base Unit এর Price
        $price = ProductPrice::where('product_id', $product->id)
            ->where('user_type_id', $request->user_type_id)
            ->where('unit_id', $baseUnitId)
            ->where('status', 1)
            ->value('price');

        return response()->json([
            'id' => $product->id,
            'product_name' => $product->name,
            'price' => $price ?? 0,
            'barcode' => $product->barcode,
            'strength' => $product->strength,
            'stock' => $product->stock?->quantity ?? 0,

            'category_name' => $product->category?->category_name,

            'base_unit_id' => $baseUnitId,
            'base_unit_name' => $baseUnit?->title ?? 'Piece',

            'secondary_unit_id' => $product->secondary_unit_id,
            'secondary_unit_name' => $product->secondaryUnit?->title,

            'image' => $product->image,
        ]);
    }

    public function productPrice(Request $request)
    {
        $price = ProductPrice::where('product_id', $request->product_id)
            ->where('user_type_id', $request->user_type_id)
            ->where('unit_id', $request->unit_id)
            ->where('status', 1)
            ->value('price');

        return response()->json([
            'price' => $price ?? 0
        ]);
    }


    public function customersByType(Request $request)
    {
        $typeId = $request->type_id;

        // Customer
        if ($typeId == USER_TYPE_CUSTOMER) {

            $customers = Customer::where('status', 1)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json($customers);
        }

        // Dealer / Agent
        if (
            $typeId == USER_TYPE_DEALER ||
            $typeId == USER_TYPE_AGENT
        ) {

            $customers = Partner::where('status', 1)
                ->where('type', $typeId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json($customers);
        }

        return response()->json([]);
    }
}
