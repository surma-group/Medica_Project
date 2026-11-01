<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\UserType;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\OrderTransfer;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index()
    {
        $orders = Order::with([
            'userType',
            'customer',
            'partner'
        ])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function accepted(Request $request)
    {
        $query = Order::with([
            'userType',
            'customer',
            'partner',
            'items.product'
        ])->where('status', ORDER_STATUS_ACCEPTED);

        $fromDate = $request->from_date
            ?? now()->subDays(7)->toDateString();

        $toDate = $request->to_date
            ?? now()->toDateString();

        $query->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate);

        if ($request->filled('user_type')) {
            $query->where('user_type_id', $request->user_type);
        }

        $status = $request->status;

        // 1. Just Accepted
        // Admin এখনো Dealer-এর কাছে Transfer তৈরি করেনি
        if ($status === 'just_accepted') {

            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('order_transfers as ot')
                    ->whereColumn('ot.order_id', 'orders.id')
                    ->where('ot.from_type', USER_TYPE_ADMIN)
                    ->where('ot.to_type', USER_TYPE_DEALER)
                    ->where('ot.status', '!=', 4);
            });
        }

        // 2. Dealer Assigned
        // Admin থেকে Dealer-এর কাছে Transfer তৈরি হয়েছে
        elseif ($status === 'dealer_assigned') {

            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('order_transfers as ot')
                    ->whereColumn('ot.order_id', 'orders.id')
                    ->where('ot.from_type', USER_TYPE_ADMIN)
                    ->where('ot.to_type', USER_TYPE_DEALER)
                    ->where('ot.status', '!=', 4);
            });
        }

        // 3. Delivery Man Assigned
        // Dealer থেকে Agent-এর কাছে Transfer তৈরি হয়েছে
        // এবং Delivery Man Assign করা হয়েছে
        elseif ($status === 'delivery_man_assigned') {

            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('order_transfers as ot')
                    ->whereColumn('ot.order_id', 'orders.id')
                    ->where('ot.from_type', USER_TYPE_DEALER)
                    ->where('ot.to_type', USER_TYPE_AGENT)
                    ->whereNotNull('ot.assigned_employee_id')
                    ->where('ot.status', '!=', 4);
            });
        }

        // 4. Dealer Accepted
        // Admin থেকে Dealer-এর Transfer Received হয়েছে
        elseif ($status === 'dealer_accepted') {

            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('order_transfers as ot')
                    ->whereColumn('ot.order_id', 'orders.id')
                    ->where('ot.from_type', USER_TYPE_ADMIN)
                    ->where('ot.to_type', USER_TYPE_DEALER)
                    ->where('ot.status', 3);
            });
        }

        // 5. Completed
        // Agent সব পণ্যের সম্পূর্ণ Quantity Receive করেছে
        elseif ($status === 'completed') {

            $query->whereRaw("
            (
                SELECT COALESCE(SUM(oti.quantity_received), 0)
                FROM order_transfer_items AS oti
                INNER JOIN order_transfers AS ot
                    ON ot.id = oti.order_transfer_id
                WHERE ot.order_id = orders.id
                  AND ot.from_type = ?
                  AND ot.to_type = ?
                  AND ot.status = 3
            ) >= (
                SELECT COALESCE(SUM(oi.quantity), 0)
                FROM order_items AS oi
                WHERE oi.order_id = orders.id
            )
        ", [
                USER_TYPE_DEALER,
                USER_TYPE_AGENT
            ]);
        }


        $orders = $query->latest()
            ->paginate(20)
            ->appends($request->all());

        $userTypes = UserType::where('status', 1)->get();

        $orderIds = $orders->getCollection()->pluck('id');

        $orderedQuantities = DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->select(
                'order_id',
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('order_id')
            ->pluck('total_quantity', 'order_id');

        $transferredQuantities = DB::table('order_transfer_items as oti')
            ->join(
                'order_transfers as ot',
                'oti.order_transfer_id',
                '=',
                'ot.id'
            )
            ->whereIn('ot.order_id', $orderIds)
            ->where('ot.from_type', USER_TYPE_ADMIN)
            ->where('ot.to_type', USER_TYPE_DEALER)
            ->whereIn('ot.status', [2, 3])
            ->select(
                'ot.order_id',
                DB::raw('SUM(oti.quantity_sent) as total_quantity')
            )
            ->groupBy('ot.order_id')
            ->pluck('total_quantity', 'order_id');

        $orders->getCollection()->transform(function ($order) use (
            $orderedQuantities,
            $transferredQuantities
        ) {
            $orderedQty = (float) ($orderedQuantities[$order->id] ?? 0);

            $transferredQty = (float) (
                $transferredQuantities[$order->id] ?? 0
            );

            $remainingQty = max(0, $orderedQty - $transferredQty);

            $order->ordered_qty_total = $orderedQty;
            $order->transferred_qty_total = $transferredQty;
            $order->remaining_qty_total = $remainingQty;

            if ($transferredQty <= 0) {
                $order->transfer_status_label = 'Not Assigned';
            } elseif ($remainingQty <= 0.000001) {
                $order->transfer_status_label = 'Fully Sent';
            } else {
                $order->transfer_status_label = 'Partially Sent';
            }

            return $order;
        });

        $transitOfficerSetting = Setting::where('slug', 'transitOfficer')
            ->where('status', 1)
            ->first();

        $transitOfficerDesignationId = $transitOfficerSetting?->value;

        $transitOfficers = collect();

        if ($transitOfficerDesignationId) {
            $transitOfficers = Employee::where(
                'designation_id',
                $transitOfficerDesignationId
            )
                ->where('status', 1)
                ->orderBy('full_name')
                ->get();
        }

        $medicineList = DB::table('order_items')
            ->join(
                'products',
                'order_items.product_id',
                '=',
                'products.id'
            )
            ->leftJoin(
                'product_companies',
                'products.company_id',
                '=',
                'product_companies.id'
            )
            ->join(
                'orders',
                'order_items.order_id',
                '=',
                'orders.id'
            )
            ->where('orders.status', ORDER_STATUS_ACCEPTED)
            ->whereBetween('orders.created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ])
            ->select(
                'products.name as medicine_name',
                'products.strength as strength',
                'product_companies.company_name as company_name',
                DB::raw('SUM(order_items.quantity) as total_qty')
            )
            ->groupBy(
                'products.name',
                'products.strength',
                'product_companies.company_name'
            )
            ->get();


        return view('admin.orders.accepted', compact(
            'orders',
            'userTypes',
            'medicineList',
            'transitOfficers'
        ));
    }

    public function track($id)
    {
        // Find Order
        $order = Order::with([
            'items.product',
        ])->findOrFail($id);

        // Find all transfers related to this order
        $transfers = OrderTransfer::with([
            'assignedEmployee',
            'sourceTransfer',
            'items.orderItem.product',
        ])
            ->where('order_id', $order->id)
            ->orderBy('created_at')
            ->get();

        return view('admin.orders.track', compact(
            'order',
            'transfers'
        ));
    }

    /**
     * Show single order details
     */
    public function show($id)
    {
        $order = Order::with([
            'userType',
            'customer',
            'partner',
            'items.product'
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Optional: update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Order status updated successfully!');
    }

    public function transferItems(Order $order)
    {
        abort_unless(
            $order->status == ORDER_STATUS_ACCEPTED
                && $order->user_type_id == USER_TYPE_AGENT,
            404
        );

        $items = $order->items()
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name ?? 'Unknown Product',
                    'strength' => $item->product->strength ?? null,
                    'quantity' => (float) $item->quantity,
                ];
            });

        return response()->json([
            'items' => $items,
        ]);
    }
}
