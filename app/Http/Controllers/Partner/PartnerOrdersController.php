<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCompany;
use App\Models\Unit;
use App\Models\Partner;
use App\Models\OrderTransfer;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\MarketingRelation;

class PartnerOrdersController extends Controller
{
    /**
     * ORDER LIST
     */
    public function index()
    {
        $user = Auth::user();

        // Logged-in Agent
        $partner = Partner::where('user_id', $user->id)
            ->where('type', USER_TYPE_AGENT)
            ->firstOrFail();

        // Only Agent's own orders
        $orders = Order::with('customer')
            ->where('reference_id', $partner->id)
            ->where('user_type_id', USER_TYPE_AGENT)
            ->latest()
            ->paginate(20);

        // Transfers sent to this Agent
        $agentTransfers = OrderTransfer::with([
            'assignedEmployee',
            'items.orderItem.product',
        ])
            ->where('from_type', USER_TYPE_DEALER)
            ->where('to_type', USER_TYPE_AGENT)
            ->where('to_id', $partner->id)
            ->whereIn('order_id', $orders->pluck('id'))
            ->latest()
            ->get()
            ->groupBy('order_id');

        // Get Delivery Man designation from Settings
        $deliveryManSetting = Setting::where('slug', 'deliveryMan')
            ->where('status', 1)
            ->first();

        // Get active employees of that designation
        $deliveryMen = collect();

        if ($deliveryManSetting) {
            $deliveryMen = Employee::where('designation_id', $deliveryManSetting->value)
                ->where('status', 1)
                ->orderBy('full_name')
                ->get();
        }

        return view('partner.orders.index', compact(
            'orders',
            'agentTransfers',
            'deliveryMen'
        ));
    }

    /**
     * CREATE ORDER PAGE (SHOP STYLE)
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $brands = Brand::where('status', 1)->get();
        $companies = ProductCompany::where('status', 1)->get();

        $products = Product::with([
            'category',
            'brand',
            'company',
            'baseUnit'
        ])
            ->where('status', 1)
            ->latest()
            ->get();

        $units = Unit::all();

        return view('partner.orders.create', compact(
            'categories',
            'brands',
            'companies',
            'products',
            'units'
        ));
    }

    /**
     * STORE ORDER 
     */
    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required'
        ]);

        $cart = json_decode($request->cart, true);

        if (empty($cart)) {
            return back()->with('error', 'Cart is empty');
        }

        $user = auth()->user();

        $partner = \App\Models\Partner::where('user_id', $user->id)->first();

        if (!$partner) {
            return back()->with('error', 'Partner not found for this user');
        }

        // ================= CREATE ORDER =================
        $order = Order::create([
            'user_type_id'   => Auth::user()->type,
            'reference_id'   => $partner->id,
            'order_number'   => 'ORD-' . strtoupper(Str::random(8)),
            'subtotal'       => 0,
            'delivery_charge' => 0,
            'discount'       => 0,
            'grand_total'    => 0,
            'payment_method' => 1,
            'status'         => 0,
            'note'           => null,
        ]);

        $subtotal = 0;

        // ================= ORDER ITEMS =================
        foreach ($cart as $item) {

            $total = $item['price'] * $item['qty'];
            $subtotal += $total;

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['id'],
                'price'      => $item['price'],
                'quantity'   => $item['qty'],   // ✅ FIXED
                'total'      => $total,
                'unit_id'    => $item['unit_id'] ?? null, // ✅ optional
                'status'     => 1
            ]);
        }

        // ================= UPDATE TOTAL =================
        $order->update([
            'subtotal'    => $subtotal,
            'grand_total' => $subtotal
        ]);

        return redirect()
            ->route('partner.orders')
            ->with('success', 'Order placed successfully');
    }

    /**
     * SHOW ORDER (INVOICE STYLE)
     */
    public function show($id)
    {
        $user = Auth::user();

        // get partner safely
        $partner = \App\Models\Partner::where('user_id', $user->id)->first();

        if (!$partner) {
            abort(403, 'Partner not found');
        }

        $order = Order::with(['items', 'userType'])
            ->where('id', $id)
            ->where('reference_id', $partner->id)
            ->where('user_type_id', $user->type)
            ->firstOrFail();

        return view('partner.orders.show', compact('order'));
    }

    /**
     * EDIT ORDER (STATUS ONLY)
     */
    public function edit($id)
    {
        $user = Auth::user();

        // get partner safely
        $partner = \App\Models\Partner::where('user_id', $user->id)->first();

        if (!$partner) {
            return redirect()
                ->route('partner.orders.index')
                ->with('error', 'Partner not found');
        }

        // get order with items + product relation
        $order = Order::with(['items.product'])
            ->where('id', $id)
            ->where('reference_id', $partner->id)
            ->firstOrFail();

        // ONLY allow edit if pending
        if ($order->status != ORDER_STATUS_PENDING) {
            return redirect()
                ->route('partner.orders.index')
                ->with('error', 'Only pending orders can be edited.');
        }

        /* ================= CART PREP FOR JS ================= */
        $cart = $order->items->map(function ($item) {
            return [
                'id'    => $item->product_id,
                'name'  => $item->product->name ?? 'Product',
                'price' => (float) $item->price,
                'qty'   => (int) $item->quantity,
            ];
        });

        /* ================= SAME DATA AS CREATE ================= */
        $categories = Category::where('status', 1)->get();
        $brands     = Brand::where('status', 1)->get();
        $companies  = ProductCompany::where('status', 1)->get();

        $products = Product::with([
            'category',
            'brand',
            'company',
            'baseUnit'
        ])
            ->where('status', 1)
            ->latest()
            ->get();

        $units = Unit::all();

        return view('partner.orders.edit', compact(
            'order',
            'cart',
            'categories',
            'brands',
            'companies',
            'products',
            'units'
        ));
    }

    /**
     * UPDATE ORDER STATUS
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cart'   => 'required',
        ]);

        $cart = json_decode($request->cart, true);

        if (empty($cart)) {
            return back()->with('error', 'Cart is empty');
        }

        $user = Auth::user();

        // get partner safely
        $partner = \App\Models\Partner::where('user_id', $user->id)->first();

        if (!$partner) {
            return back()->with('error', 'Partner not found');
        }

        // get order
        $order = Order::where('id', $id)
            ->where('reference_id', $partner->id)
            ->firstOrFail();

        // only pending can update
        if ($order->status != ORDER_STATUS_PENDING) {
            return back()->with('error', 'Only pending orders can be updated');
        }

        $subtotal = 0;

        // ================= DELETE OLD ITEMS =================
        OrderItem::where('order_id', $order->id)->delete();

        // ================= INSERT NEW ITEMS =================
        foreach ($cart as $item) {

            $total = $item['price'] * $item['qty'];
            $subtotal += $total;

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['id'],
                'price'      => $item['price'],
                'quantity'   => $item['qty'],
                'total'      => $total,
                'unit_id'    => $item['unit_id'] ?? null,
                'status'     => 1
            ]);
        }

        // ================= UPDATE ORDER =================
        $order->update([
            'subtotal'    => $subtotal,
            'grand_total' => $subtotal,
        ]);

        return redirect()
            ->route('partner.orders')
            ->with('success', 'Order updated successfully');
    }

    /**
     * DELETE ORDER (optional)
     */
    public function destroy($id)
    {
        $partner = Auth::user()->partner;

        $order = OrderRequest::where('partner_id', $partner->id)
            ->findOrFail($id);

        if ($order->items()) {
            $order->items()->delete();
        }

        $order->delete();

        return redirect()
            ->route('partner.orders')
            ->with('success', 'Order deleted successfully');
    }

    public function companyProducts($companyId)
    {
        $products = Product::where('company_id', $companyId)
            ->where('status', 1)
            ->select('id', 'name', 'strength')
            ->orderBy('name')
            ->get();

        return response()->json($products);
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

        // dynamic price
        $price = optional($product->prices->first())->price;

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $price ?? 0,
            'image' => $image,
            'barcode' => $product->barcode,
            'strength' => $product->strength,
            'manufacturer_name' => $product->manufacturer_name,
        ]);
    }

    /**
     * TRACK AGENT ORDER
     */
    public function track($id)
    {
        $user = Auth::user();

        // Find the logged-in Agent's Partner record
        $partner = Partner::where('user_id', $user->id)
            ->where('type', USER_TYPE_AGENT)
            ->firstOrFail();

        // Only allow tracking the logged-in Agent's own order
        $order = Order::with([
            'items.product',
        ])
            ->where('id', $id)
            ->where('reference_id', $partner->id)
            ->where('user_type_id', USER_TYPE_AGENT)
            ->firstOrFail();

        // Admin -> Dealer transfers and Dealer -> Agent transfers
        $transfers = OrderTransfer::with([
            'assignedEmployee',
            'sourceTransfer',
            'items.orderItem.product',
        ])
            ->where('order_id', $order->id)
            ->where(function ($query) use ($partner) {
                $query->where(function ($q) {
                    $q->where('from_type', USER_TYPE_ADMIN)
                        ->where('to_type', USER_TYPE_DEALER);
                })->orWhere(function ($q) use ($partner) {
                    $q->where('from_type', USER_TYPE_DEALER)
                        ->where('to_type', USER_TYPE_AGENT)
                        ->where('to_id', $partner->id);
                });
            })
            ->orderBy('created_at')
            ->get();

        return view('partner.orders.track', compact(
            'order',
            'transfers'
        ));
    }
}
