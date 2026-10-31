<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTransfer;
use App\Models\OrderTransferItem;
use App\Models\Partner;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\MarketingRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderTransferController extends Controller
{
    /**
     * Admin assigns a Transit Officer and sends
     * Agent order products to the Agent's assigned Dealer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => [
                'required',
                'integer',
                'exists:orders,id',
            ],

            'assigned_employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.order_item_id' => [
                'required',
                'integer',
                'distinct',
            ],

            'items.*.quantity_sent' => [
                'required',
                'numeric',
                'min:0.001',
            ],

            'note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $adminId = auth('admin')->id();

        DB::transaction(function () use ($validated, $adminId) {

            /*
            |--------------------------------------------------------------------------
            | 1. Validate Order
            |--------------------------------------------------------------------------
            */

            $order = Order::whereKey($validated['order_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (
                (int) $order->user_type_id !== USER_TYPE_AGENT ||
                (int) $order->status !== ORDER_STATUS_ACCEPTED
            ) {
                throw ValidationException::withMessages([
                    'order_id' =>
                        'Only accepted Agent orders can be transferred.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Validate Agent
            |--------------------------------------------------------------------------
            */

            $agent = Partner::whereKey($order->reference_id)
                ->where('type', USER_TYPE_AGENT)
                ->where('status', 1)
                ->first();

            if (!$agent) {
                throw ValidationException::withMessages([
                    'order_id' =>
                        'The Agent associated with this order is invalid or inactive.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Find Agent's Assigned Dealer
            |--------------------------------------------------------------------------
            */

            $relation = MarketingRelation::where(
                    'parent_type',
                    USER_TYPE_DEALER
                )
                ->where('child_type', USER_TYPE_AGENT)
                ->where('child_id', $agent->id)
                ->where('status', 1)
                ->first();

            if (!$relation) {
                throw ValidationException::withMessages([
                    'order_id' =>
                        'No active Dealer is assigned to this Agent.',
                ]);
            }

            $dealer = Partner::whereKey($relation->parent_id)
                ->where('type', USER_TYPE_DEALER)
                ->where('status', 1)
                ->first();

            if (!$dealer) {
                throw ValidationException::withMessages([
                    'order_id' =>
                        'The assigned Dealer is invalid or inactive.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 4. Get Transit Officer Designation from Settings
            |--------------------------------------------------------------------------
            */

            $transitOfficerSetting = Setting::where(
                    'slug',
                    'transitOfficer'
                )
                ->where('status', 1)
                ->first();

            if (
                !$transitOfficerSetting ||
                !is_numeric($transitOfficerSetting->value) ||
                (int) $transitOfficerSetting->value <= 0
            ) {
                throw ValidationException::withMessages([
                    'assigned_employee_id' =>
                        'Please configure an active Transit Officer designation in Settings.',
                ]);
            }

            $designationId = (int) $transitOfficerSetting->value;

            /*
            |--------------------------------------------------------------------------
            | 5. Validate Selected Transit Officer
            |--------------------------------------------------------------------------
            */

            $employee = Employee::whereKey(
                    $validated['assigned_employee_id']
                )
                ->where('designation_id', $designationId)
                ->where('status', 1)
                ->first();

            if (!$employee) {
                throw ValidationException::withMessages([
                    'assigned_employee_id' =>
                        'Please select an active Employee with the configured Transit Officer designation.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 6. Validate Order Items and Remaining Quantities
            |--------------------------------------------------------------------------
            */

            $transferItems = [];

            foreach ($validated['items'] as $itemData) {

                $quantitySent = (float) $itemData['quantity_sent'];

                if (!is_finite($quantitySent) || $quantitySent <= 0) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'Send quantity must be greater than zero.',
                    ]);
                }

                // The selected item must belong to this order.
                $orderItem = OrderItem::whereKey(
                        $itemData['order_item_id']
                    )
                    ->where('order_id', $order->id)
                    ->lockForUpdate()
                    ->first();

                if (!$orderItem) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'An invalid product was selected for this order.',
                    ]);
                }

                // Exclude cancelled transfers from the total.
                $previouslyTransferred = OrderTransferItem::where(
                        'order_item_id',
                        $orderItem->id
                    )
                    ->whereHas('orderTransfer', function ($query) {
                        $query->where('status', '!=', 4);
                    })
                    ->sum('quantity_sent');

                $orderedQuantity = (float) $orderItem->quantity;

                $remainingQuantity = $orderedQuantity
                    - (float) $previouslyTransferred;

                if ($remainingQuantity < 0) {
                    $remainingQuantity = 0;
                }

                if ($quantitySent > $remainingQuantity + 0.000001) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'Send quantity exceeds the remaining quantity for product ID '
                            . $orderItem->product_id
                            . '. Remaining quantity: '
                            . number_format($remainingQuantity, 3, '.', ''),
                    ]);
                }

                $transferItems[] = [
                    'order_item_id' => $orderItem->id,
                    'quantity_sent' => $quantitySent,
                ];
            }

            if (empty($transferItems)) {
                throw ValidationException::withMessages([
                    'items' => 'No valid products were selected.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 7. Generate Transfer Number
            |--------------------------------------------------------------------------
            */

            do {
                $transferNumber = 'TRF-'
                    . now()->format('YmdHis')
                    . '-'
                    . Str::upper(Str::random(6));

            } while (
                OrderTransfer::where(
                    'transfer_number',
                    $transferNumber
                )->exists()
            );

            /*
            |--------------------------------------------------------------------------
            | 8. Create Admin -> Dealer Transfer
            |--------------------------------------------------------------------------
            */

            $transfer = OrderTransfer::create([
                'transfer_number' => $transferNumber,
                'order_id' => $order->id,

                'from_type' => USER_TYPE_ADMIN,
                'from_id' => $adminId,

                'to_type' => USER_TYPE_DEALER,
                'to_id' => $dealer->id,

                'assigned_employee_id' => $employee->id,

                // 2 = Sent
                'status' => 2,
                'sent_at' => now(),
                'received_at' => null,

                'note' => $validated['note'] ?? null,
                'created_by' => $adminId,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 9. Save Transfer Items
            |--------------------------------------------------------------------------
            */

            foreach ($transferItems as $transferItem) {
                OrderTransferItem::create([
                    'order_transfer_id' => $transfer->id,
                    'order_item_id' => $transferItem['order_item_id'],
                    'quantity_sent' => $transferItem['quantity_sent'],
                    'quantity_received' => null,
                ]);
            }
        });

        return redirect()
            ->back()
            ->with(
                'success',
                'Products successfully assigned to the Transit Officer and sent to the Dealer.'
            );
    }
}