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
        /*
    |--------------------------------------------------------------------------
    | Accepted Orders Query
    |--------------------------------------------------------------------------
    */

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

        $orders = $query->latest()
            ->paginate(20)
            ->appends($request->all());

        $userTypes = UserType::where('status', 1)->get();


        /*
        |--------------------------------------------------------------------------
        | Calculate Order Transfer Status
        |--------------------------------------------------------------------------
        | Not Assigned   = কোনো পণ্য Transfer করা হয়নি
        | Partially Sent = কিছু পণ্য Transfer হয়েছে, কিছু বাকি
        | Fully Sent     = সব পণ্যের সম্পূর্ণ Quantity Transfer হয়েছে
        */

        $orderIds = $orders->getCollection()->pluck('id');

        // প্রতিটি Order-এর মোট Ordered Quantity
        $orderedQuantities = DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->select(
                'order_id',
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('order_id')
            ->pluck('total_quantity', 'order_id');

        /*
        * শুধু Sent এবং Received Transfer-এর Quantity গণনা হবে।
        * Status: 2 = Sent, 3 = Received
        * Pending, Assigned এবং Cancelled Transfer গণনা হবে না।
        */
        $transferredQuantities = DB::table('order_transfer_items as oti')
            ->join(
                'order_transfers as ot',
                'oti.order_transfer_id',
                '=',
                'ot.id'
            )
            ->whereIn('ot.order_id', $orderIds)
            ->whereIn('ot.status', [2, 3])
            ->select(
                'ot.order_id',
                DB::raw('SUM(oti.quantity_sent) as total_quantity')
            )
            ->groupBy('ot.order_id')
            ->pluck('total_quantity', 'order_id');

        // প্রতিটি Order-এর Transfer Status নির্ধারণ
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


        /*
        |--------------------------------------------------------------------------
        | Transit Officer Designation from Settings
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Medicine Summary
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view('admin.orders.accepted', compact(
            'orders',
            'userTypes',
            'medicineList',
            'transitOfficers'
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
