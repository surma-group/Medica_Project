<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\ProductCompany;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Services\StockService;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->format('Y-m-d'));
        $dateTo   = $request->input('date_to', now()->format('Y-m-d'));

        $query = Purchase::with([
            'items',
            'userType',
            'supplier'
        ]);

        // Date From
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        // Date To
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Supplier
        if ($request->filled('supplier_id')) {
            $query->where('reference_id', $request->supplier_id)
                ->where('user_type_id', USER_TYPE_SUPPLIER);
        }

        // Purchase Number
        if ($request->filled('purchase_number')) {
            $query->where(
                'purchase_number',
                'like',
                '%' . $request->purchase_number . '%'
            );
        }

        $purchases = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Supplier dropdown
        $suppliers = Supplier::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.purchases.index', compact(
            'purchases',
            'suppliers'
        ));
    }

    public function create()
    {
        $companies = ProductCompany::where('status', 1)->get();

        $categories = Category::where('status', 1)->get();

        $units = Unit::all();

        $suppliers = Supplier::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.purchases.create', compact(
            'companies',
            'categories',
            'units',
            'suppliers'
        ));
    }



    public function store(Request $request, StockService $stockService, AccountingService $accountingService)
    {
        // ১. জেসন ডিকোডিং (নিরাপদ করার জন্য)
        if ($request->has('items') && is_string($request->items)) {
            $decodedItems = json_decode($request->items, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['items' => $decodedItems]);
            }
        }

        // ২. ভ্যালিডেশন
        $request->validate([
            'purchase_date' => 'required|date',
            'supplier_id'   => 'nullable|exists:suppliers,id',
            'items'         => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.price'      => 'required|numeric|min:0',
            'items.*.quantity'   => 'required|numeric|min:1',
            'items.*.unit_id'    => 'required|exists:units,id',
        ]);

        // ৩. পার্চেজের মোট টাকা (Grand Total) আগে হিসাব করে নেওয়া
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $deliveryCharge = 0;
        $discount = 0;
        $grandTotal = ($subtotal + $deliveryCharge) - $discount;

        // 🛑 ৪. কোম্পানির ব্যালেন্স চেক (আপনার দেওয়া লজিক)
        $companyBalance = DB::table('ledger_entry as le')
            ->join('ledger as l', 'l.id', '=', 'le.ledger_id')
            ->where('l.type', 1)
            ->whereIn('l.id', [L_CASH_DESK, L_BANK_MAIN])
            ->selectRaw('COALESCE(SUM(le.debit) - SUM(le.credit), 0) as balance')
            ->value('balance');

        // ব্যালেন্স কম থাকলে ট্রানজেকশন শুরু করার আগেই ব্যাক করবে
        if ($grandTotal > $companyBalance) {
            return redirect()->back()
                ->with('error', 'Insufficient company balance to complete this purchase.')
                ->withInput(); // ইনপুটগুলো ধরে রাখার জন্য
        }

        // ৫. ব্যালেন্স ঠিক থাকলে ডাটাবেস এন্ট্রি শুরু হবে
        DB::beginTransaction();

        try {
            $purchase = Purchase::create([
                'user_type_id'    => USER_TYPE_SUPPLIER,
                'reference_id'    => $request->supplier_id,
                'purchase_number' => 'PUR-' . now()->format('YmdHis') . rand(100, 999),
                'subtotal'        => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'discount'        => $discount,
                'grand_total'     => $grandTotal,
                'payment_method'  => 1,
                'status'          => 1,
                'note'            => $request->note ?? 'New Purchase',
            ]);

            foreach ($request->items as $item) {
                $itemTotal = $item['price'] * $item['quantity'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $item['product_id'],
                    'price'       => $item['price'],
                    'quantity'    => $item['quantity'],
                    'total'       => $itemTotal,
                    'unit_id'     => $item['unit_id'],
                    'status'      => 1,
                ]);

                $stockService->increaseStock(
                    productId: $item['product_id'],
                    quantity: $item['quantity'],
                    unitId: $item['unit_id'],
                    referenceId: $purchase->id,
                    referenceType: 'Purchase',
                    note: 'Stock added from purchase #' . $purchase->purchase_number
                );
            }

            // অ্যাকাউন্টিং এন্ট্রি (আমরা আগের ধাপে L_INVENTORY কনস্ট্যান্ট সেট করেছি)
            $accountingService->addTransaction(
                amount: $grandTotal,
                debitLedgerId: L_INVENTORY,
                creditLedgerId: L_CASH_DESK,
                voucherType: VT_PURCHASE,
                referenceId: $purchase->id,
                note: 'Purchase #' . $purchase->purchase_number
            );

            DB::commit();

            return redirect()
                ->route('purchases.index')
                ->with('success', 'Purchase created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function show($id)
    {
        $purchase = Purchase::with([
            'items.product',
            'items.unit',
            'supplier',
            'userType'
        ])->findOrFail($id);

        return view('admin.purchases.show', compact('purchase'));
    }

    public function companyProducts($companyId, $categoryId = null)
    {
        $query = Product::where('company_id', $companyId)
            ->where('status', 1);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return response()->json(
            $query->select('id', 'name', 'strength')->orderBy('name')->get()
        );
    }

    public function productDetails($productId)
    {
        $userType = auth()->user()->type;

        $product = Product::with(['prices' => function ($q) use ($userType) {
            $q->where('user_type_id', $userType)
                ->where('status', 1);
        }])
            ->where('id', $productId)
            ->firstOrFail();

        $defaultImage = url('/website/img/product.jpg');

        $image = !empty($product->image)
            ? asset('public/storage/' . $product->image)
            : $defaultImage;

        $price = optional($product->prices->first())->price;

        // 🔥 DEFAULT UNIT LOGIC (your existing constant)
        $baseUnitId = $product->base_unit_id ?? DEFAULT_UNIT;

        $baseUnit = Unit::find($baseUnitId);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $price ?? 0,
            'image' => $image,
            'barcode' => $product->barcode,
            'strength' => $product->strength,
            'manufacturer_name' => $product->manufacturer_name,

            // UNIT DATA
            'base_unit_id' => $baseUnitId,
            'base_unit_name' => $baseUnit?->title ?? 'Piece',

            'secondary_unit_id' => $product->secondary_unit_id,
            'secondary_unit_name' => $product->secondaryUnit?->title,

            'conversion_rate' => $product->conversion_rate,
        ]);
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
}
