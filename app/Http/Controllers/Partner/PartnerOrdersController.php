<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


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
use App\Services\AccountingService;
use App\Models\ProductPrice;

class PartnerOrdersController extends Controller
{
    /**
     * ORDER LIST
     */


    public function index(Request $request)
    {
        $user = Auth::user();

        // Logged-in Agent
        $partner = Partner::where('user_id', $user->id)
            ->where('type', USER_TYPE_AGENT)
            ->firstOrFail();

        // Default Date Range
        $fromDate = $request->input(
            'from_date',
            now()->subMonth()->format('Y-m-d')
        );

        $toDate = $request->input(
            'to_date',
            now()->format('Y-m-d')
        );

        // Default Status
        $status = $request->input('status', 'pending');

        if (!in_array($status, ['pending', 'completed'])) {
            $status = 'pending';
        }

        // ================= ORDERS QUERY =================

        $ordersQuery = Order::with([
            'customer',
            'items.product',
        ])
            ->where('reference_id', $partner->id)
            ->where('user_type_id', USER_TYPE_AGENT)
            ->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59',
            ]);

        // ================= TRANSFERS QUERY =================

        $transfersQuery = OrderTransfer::with([
            'assignedEmployee',
            'items.orderItem.product',
        ])
            ->where('from_type', USER_TYPE_DEALER)
            ->where('to_type', USER_TYPE_AGENT)
            ->where('to_id', $partner->id)
            ->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59',
            ]);

        // ================= STATUS FILTER =================

        if ($status === 'completed') {

            // Only orders having completed transfers
            $ordersQuery->whereHas('transfers', function ($query) use (
                $partner,
                $fromDate,
                $toDate
            ) {
                $query->where('from_type', USER_TYPE_DEALER)
                    ->where('to_type', USER_TYPE_AGENT)
                    ->where('to_id', $partner->id)
                    ->where('status', 3)
                    ->whereBetween('created_at', [
                        $fromDate . ' 00:00:00',
                        $toDate . ' 23:59:59',
                    ]);
            });

            $transfersQuery->where('status', 3);
        } else {

            // Orders having pending transfers OR no transfers yet
            $ordersQuery->where(function ($query) use (
                $partner,
                $fromDate,
                $toDate
            ) {

                $query->whereHas('transfers', function ($transferQuery) use (
                    $partner,
                    $fromDate,
                    $toDate
                ) {
                    $transferQuery->where('from_type', USER_TYPE_DEALER)
                        ->where('to_type', USER_TYPE_AGENT)
                        ->where('to_id', $partner->id)
                        ->whereIn('status', [0, 1, 2, 4])
                        ->whereBetween('created_at', [
                            $fromDate . ' 00:00:00',
                            $toDate . ' 23:59:59',
                        ]);
                })

                    ->orWhereDoesntHave('transfers');
            });

            $transfersQuery->whereIn('status', [0, 1, 2, 4]);
        }

        // ================= PAGINATION =================

        $orders = $ordersQuery
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // ================= AGENT TRANSFERS =================

        $agentTransfers = $transfersQuery
            ->whereIn('order_id', $orders->pluck('id'))
            ->latest()
            ->get()
            ->groupBy('order_id');

        return view('partner.orders.index', compact(
            'orders',
            'agentTransfers',
            'fromDate',
            'toDate',
            'status'
        ));
    }

    /**
     * CREATE ORDER PAGE (SHOP STYLE)
     */
    public function create()
    {
        $companies = ProductCompany::where('status', 1)->get();

        return view('partner.orders.create', compact('companies'));
    }

    /**
     * STORE ORDER 
     */

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|string',
        ]);

        $cart = json_decode($request->cart, true);

        if (!is_array($cart) || empty($cart)) {
            return back()->with('error', 'Cart is empty');
        }

        $user = auth()->user();

        $partner = Partner::where('user_id', $user->id)->first();

        if (!$partner) {
            return back()->with('error', 'Partner not found for this user');
        }

        $userTypeId = $user->type;

        $orderItems = [];
        $subtotal = 0;

        // ================= VALIDATE CART & GET DATABASE PRICE =================

        foreach ($cart as $item) {

            if (
                empty($item['id']) ||
                empty($item['unit_id']) ||
                !isset($item['qty']) ||
                !is_numeric($item['qty']) ||
                $item['qty'] < 1
            ) {
                return back()->with('error', 'Invalid cart item');
            }

            $product = Product::where('id', $item['id'])
                ->where('status', 1)
                ->first();

            if (!$product) {
                return back()->with('error', 'Product not found or inactive');
            }

            $unitId = $item['unit_id'];

            // Only Base Unit or Secondary Unit is allowed
            $allowedUnits = array_filter([
                $product->base_unit_id,
                $product->secondary_unit_id,
            ]);

            if (!in_array($unitId, $allowedUnits)) {
                return back()->with('error', 'Invalid unit selected');
            }

            // Get trusted price from database
            $productPrice = ProductPrice::where('product_id', $product->id)
                ->where('user_type_id', $userTypeId)
                ->where('unit_id', $unitId)
                ->where('status', 1)
                ->first();

            if (!$productPrice) {
                return back()->with('error', 'Price not found for selected product and unit');
            }

            $price = (float) $productPrice->price;
            $qty = (float) $item['qty'];

            $total = $price * $qty;

            $subtotal += $total;

            $orderItems[] = [
                'product_id' => $product->id,
                'unit_id' => $unitId,
                'price' => $price,
                'quantity' => $qty,
                'total' => $total,
                'status' => 1,
            ];
        }

        // ================= CREATE ORDER =================

        DB::transaction(function () use (
            $user,
            $partner,
            $orderItems,
            $subtotal
        ) {

            $order = Order::create([
                'user_type_id' => $user->type,
                'reference_id' => $partner->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'subtotal' => $subtotal,
                'delivery_charge' => 0,
                'discount' => 0,
                'grand_total' => $subtotal,
                'payment_method' => 1,
                'status' => 0,
                'note' => null,
            ]);

            foreach ($orderItems as $item) {

                $item['order_id'] = $order->id;

                OrderItem::create($item);
            }
        });

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

        // Logged-in Agent
        $partner = Partner::where('user_id', $user->id)
            ->where('type', USER_TYPE_AGENT)
            ->first();

        if (!$partner) {
            return redirect()
                ->route('partner.orders')
                ->with('error', 'Partner not found');
        }

        // Get Order with Items and Products
        $order = Order::with(['items.product'])
            ->where('id', $id)
            ->where('reference_id', $partner->id)
            ->where('user_type_id', USER_TYPE_AGENT)
            ->firstOrFail();

        // Only Pending Orders Can Be Edited
        if ($order->status != ORDER_STATUS_PENDING) {
            return redirect()
                ->route('partner.orders')
                ->with('error', 'Only pending orders can be edited.');
        }

        // Get Unit Names
        $unitIds = $order->items
            ->pluck('unit_id')
            ->filter()
            ->unique();

        $units = Unit::whereIn('id', $unitIds)
            ->get()
            ->keyBy('id');

        // Prepare Cart for JavaScript
        $cart = $order->items->map(function ($item) use ($units) {

            return [
                'id'        => $item->product_id,
                'name'      => $item->product->name ?? 'Product',
                'unit_id'   => $item->unit_id,
                'unit_name' => $units[$item->unit_id]->title ?? 'Unit',
                'price'     => (float) $item->price,
                'qty'       => (int) $item->quantity,
            ];
        })->values();

        // Active Companies
        $companies = ProductCompany::where('status', 1)->get();

        return view('partner.orders.edit', compact(
            'order',
            'cart',
            'companies'
        ));
    }


    /**
     * UPDATE ORDER STATUS
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cart' => 'required|string',
        ]);

        $cart = json_decode($request->cart, true);

        if (!is_array($cart) || empty($cart)) {
            return back()->with('error', 'Cart is empty');
        }

        $user = Auth::user();

        // Get Partner
        $partner = Partner::where('user_id', $user->id)
            ->where('type', USER_TYPE_AGENT)
            ->first();

        if (!$partner) {
            return back()->with('error', 'Partner not found');
        }

        // Get Order
        $order = Order::where('id', $id)
            ->where('reference_id', $partner->id)
            ->where('user_type_id', USER_TYPE_AGENT)
            ->firstOrFail();

        // Only Pending Order Can Update
        if ($order->status != ORDER_STATUS_PENDING) {
            return back()->with('error', 'Only pending orders can be updated');
        }

        $userTypeId = $user->type;

        $orderItems = [];
        $subtotal = 0;

        // Validate Cart Items & Get Database Price
        foreach ($cart as $item) {

            if (
                empty($item['id']) ||
                empty($item['unit_id']) ||
                !isset($item['qty']) ||
                !is_numeric($item['qty']) ||
                $item['qty'] < 1
            ) {
                return back()->with('error', 'Invalid cart item');
            }

            // Get Active Product
            $product = Product::where('id', $item['id'])
                ->where('status', 1)
                ->first();

            if (!$product) {
                return back()->with('error', 'Product not found or inactive');
            }

            $unitId = $item['unit_id'];

            // Check Allowed Unit
            $allowedUnits = array_filter([
                $product->base_unit_id,
                $product->secondary_unit_id,
            ]);

            if (!in_array($unitId, $allowedUnits)) {
                return back()->with('error', 'Invalid unit selected');
            }

            // Get Price From Database
            $productPrice = ProductPrice::where('product_id', $product->id)
                ->where('user_type_id', $userTypeId)
                ->where('unit_id', $unitId)
                ->where('status', 1)
                ->first();

            if (!$productPrice) {
                return back()->with('error', 'Price not found for selected product and unit');
            }

            // Never Trust Frontend Price
            $price = (float) $productPrice->price;
            $qty = (float) $item['qty'];

            $total = $price * $qty;
            $subtotal += $total;

            $orderItems[] = [
                'product_id' => $product->id,
                'unit_id'    => $unitId,
                'price'      => $price,
                'quantity'   => $qty,
                'total'      => $total,
                'status'     => 1,
            ];
        }

        // Update Order & Items Together
        DB::transaction(function () use ($order, $orderItems, $subtotal) {

            // Delete Old Items
            OrderItem::where('order_id', $order->id)->delete();

            // Insert New Items
            foreach ($orderItems as $item) {
                $item['order_id'] = $order->id;

                OrderItem::create($item);
            }

            // Update Order
            $deliveryCharge = (float) $order->delivery_charge;
            $discount = (float) $order->discount;

            $order->update([
                'subtotal'    => $subtotal,
                'grand_total' => $subtotal + $deliveryCharge - $discount,
            ]);
        });

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

        $product = Product::with([
            'baseUnit',
            'secondaryUnit',
        ])
            ->where('id', $productId)
            ->where('status', 1)
            ->firstOrFail();

        $defaultImage = url('/website/img/product.jpg');

        $image = !empty($product->image)
            ? asset('public/storage/' . $product->image)
            : $defaultImage;

        // Base Unit and Secondary Unit IDs
        $unitIds = array_filter([
            $product->base_unit_id,
            $product->secondary_unit_id,
        ]);

        // Agent Type-wise Unit Prices
        $prices = ProductPrice::with('unit')
            ->where('product_id', $product->id)
            ->where('user_type_id', $userType)
            ->whereIn('unit_id', $unitIds)
            ->where('status', 1)
            ->get();

        $units = $prices->map(function ($price) {
            return [
                'id' => $price->unit_id,
                'name' => $price->unit->title  ?? 'Unit',
                'price' => (float) $price->price,
            ];
        })->values();

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'image' => $image,
            'barcode' => $product->barcode,
            'strength' => $product->strength,
            'manufacturer_name' => $product->manufacturer_name,
            'units' => $units,
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

    public function receiveTransfer(
        $id,
        \App\Services\AccountingService $accountingService
    ) {
        $user = Auth::user();

        // Logged-in Agent
        $partner = Partner::where('user_id', $user->id)
            ->where('type', USER_TYPE_AGENT)
            ->where('status', 1)
            ->firstOrFail();

        try {

            DB::transaction(function () use (
                $id,
                $partner,
                $accountingService
            ) {

                // Find and lock the transfer
                $transfer = OrderTransfer::where('id', $id)
                    ->where('from_type', USER_TYPE_DEALER)
                    ->where('to_type', USER_TYPE_AGENT)
                    ->where('to_id', $partner->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Check transfer status
                if ((int) $transfer->status !== 2) {
                    throw new \RuntimeException(
                        'This transfer has already been received or cannot be received.'
                    );
                }

                // Check original order ownership
                $order = Order::where('id', $transfer->order_id)
                    ->where('reference_id', $partner->id)
                    ->where('user_type_id', USER_TYPE_AGENT)
                    ->firstOrFail();

                // Delivery Man must already be assigned by Dealer
                if (!$transfer->assigned_employee_id) {
                    throw new \RuntimeException(
                        'No Delivery Man assigned to this transfer.'
                    );
                }

                // Get and lock Agent Ledger
                $sender = Partner::where('id', $partner->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $sender->load('ledger');

                if (!$sender->ledger_id || !$sender->ledger) {
                    throw new \RuntimeException(
                        'Agent Wallet Ledger পাওয়া যায়নি।'
                    );
                }

                // Get and lock Assigned Delivery Man
                $deliveryMan = Employee::with('ledger')
                    ->where('id', $transfer->assigned_employee_id)
                    ->where('status', 1)
                    ->lockForUpdate()
                    ->first();

                if (!$deliveryMan) {
                    throw new \RuntimeException(
                        'Assigned Delivery Man পাওয়া যায়নি অথবা Account Inactive।'
                    );
                }

                if (!$deliveryMan->ledger_id || !$deliveryMan->ledger) {
                    throw new \RuntimeException(
                        'Delivery Man-এর Ledger তৈরি করা নেই।'
                    );
                }

                // Get and lock transfer items
                $items = $transfer->items()
                    ->with('orderItem')
                    ->lockForUpdate()
                    ->get();

                if ($items->isEmpty()) {
                    throw new \RuntimeException(
                        'No products found in this transfer.'
                    );
                }

                // Calculate this transfer's total amount
                $transferAmount = 0;

                foreach ($items as $item) {

                    if (!$item->orderItem) {
                        throw new \RuntimeException(
                            'Order item information পাওয়া যায়নি।'
                        );
                    }

                    $unitPrice = (float) $item->orderItem->price;
                    $quantity = (float) $item->quantity_sent;

                    $transferAmount += $quantity * $unitPrice;
                }

                $transferAmount = round($transferAmount, 2);

                if ($transferAmount <= 0) {
                    throw new \RuntimeException(
                        'Invalid transfer amount.'
                    );
                }

                // Check Agent Wallet Balance
                $balance = (float) $sender->ledger->entries()->sum('credit')
                    - (float) $sender->ledger->entries()->sum('debit');

                if ($transferAmount > $balance) {
                    throw new \RuntimeException(
                        'আপনার Wallet-এ পর্যাপ্ত Balance নেই। '
                            . 'Required: ৳' . number_format($transferAmount, 2)
                            . ', Available: ৳' . number_format($balance, 2)
                    );
                }

                // Prepare transaction note
                $note = "Order Transfer {$transfer->transfer_number}: "
                    . "{$sender->name} transferred ৳"
                    . number_format($transferAmount, 2)
                    . " to Delivery Man {$deliveryMan->full_name}";

                // Transfer Agent Wallet to Delivery Man Wallet
                $accountingService->addTransaction(
                    amount: $transferAmount,
                    debitLedgerId: $sender->ledger_id,
                    creditLedgerId: $deliveryMan->ledger_id,
                    voucherType: VT_ORDER_DELIVERY,
                    referenceId: $deliveryMan->id,
                    note: $note
                );

                // Update received quantity for each item
                foreach ($items as $item) {
                    $item->quantity_received = $item->quantity_sent;
                    $item->save();
                }

                // Update transfer status
                $transfer->status = 3;
                $transfer->received_at = now();
                $transfer->save();
            }, 3);

            return redirect()
                ->route('partner.orders')
                ->with('success', 'Order transfer received and payment transferred successfully.');
        } catch (\RuntimeException $e) {

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {

            report($e);

            \Log::error('Agent receive transfer failed', [
                'transfer_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Transfer Receive ব্যর্থ হয়েছে। Laravel log পরীক্ষা করুন।');
        }
    }
}
