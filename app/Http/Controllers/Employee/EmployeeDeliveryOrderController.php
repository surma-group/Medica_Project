<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\OrderTransfer;
use App\Models\Partner;

class EmployeeDeliveryOrderController extends Controller
{
    /**
     * Get logged-in Delivery Man employee.
     */
    private function getDeliveryMan()
    {
        $employee = Employee::where('user_id', Auth::id())
            ->where('status', 1)
            ->firstOrFail();

        $setting = Setting::where('slug', 'deliveryMan')
            ->where('status', 1)
            ->first();

        if (
            !$setting ||
            (int) $employee->designation_id !== (int) $setting->value
        ) {
            abort(403, 'You are not authorized to access Delivery Management.');
        }

        return $employee;
    }

    /**
     * Delivery Order List
     */
    public function index(Request $request)
    {
        $employee = $this->getDeliveryMan();

        // Default filter: Assigned
        $filter = $request->query('filter', 'assigned');

        if (!in_array($filter, ['assigned', 'delivered', 'all'])) {
            $filter = 'assigned';
        }

        $query = OrderTransfer::with([
            'order',
            'assignedEmployee',
            'items.orderItem.product',
        ])
            ->where('assigned_employee_id', $employee->id)
            ->where('from_type', USER_TYPE_DEALER)
            ->where('to_type', USER_TYPE_AGENT);

        // Apply status filter
        if ($filter === 'assigned') {
            $query->where('status', 2);
        } elseif ($filter === 'delivered') {
            $query->where('status', 3);
        } else {
            $query->whereIn('status', [2, 3]);
        }

        $transfers = $query->latest('id')->paginate(20)->withQueryString();

        // Get Agent information
        $agentIds = $transfers->pluck('to_id')->unique()->filter();

        $agents = Partner::whereIn('id', $agentIds)
            ->get()
            ->keyBy('id');

        // Calculate transfer amount
        foreach ($transfers as $transfer) {
            $transfer->agent = $agents->get($transfer->to_id);

            $transfer->transfer_amount = $transfer->items->sum(function ($item) {
                if (!$item->orderItem) {
                    return 0;
                }

                return (float) $item->quantity_sent
                    * (float) $item->orderItem->price;
            });

            $transfer->status_label = $transfer->status == 2
                ? 'Assigned'
                : 'Delivered';
        }

        return view('employee.delivery-orders.index', compact(
            'transfers',
            'employee',
            'filter'
        ));
    }

    /**
     * Delivery Order Details
     */
    public function show($id)
    {
        $employee = $this->getDeliveryMan();

        // Only this employee's assigned transfer can be accessed
        $transfer = OrderTransfer::with([
            'order',
            'assignedEmployee',
            'items.orderItem.product',
        ])
            ->where('id', $id)
            ->where('assigned_employee_id', $employee->id)
            ->where('from_type', USER_TYPE_DEALER)
            ->where('to_type', USER_TYPE_AGENT)
            ->whereIn('status', [2, 3])
            ->firstOrFail();

        $agent = Partner::where('id', $transfer->to_id)->first();

        $transfer->agent = $agent;

        $transfer->transfer_amount = $transfer->items->sum(function ($item) {
            if (!$item->orderItem) {
                return 0;
            }

            return (float) $item->quantity_sent
                * (float) $item->orderItem->price;
        });

        $transfer->status_label = match ((int) $transfer->status) {
            2 => 'Sent',
            3 => 'Received',
            default => 'Unknown',
        };

        return view('employee.delivery-orders.show', compact(
            'transfer',
            'employee',
            'agent'
        ));
    }
}
