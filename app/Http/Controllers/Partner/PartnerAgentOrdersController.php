<?php

namespace App\Http\Controllers\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\Order;
use App\Models\Partner;
use App\Models\MarketingRelation;

class PartnerAgentOrdersController extends Controller
{
    /**
     * Get the logged-in Dealer and verify access.
     */
    private function getDealer(): Partner
    {
        $dealer = Partner::where('user_id', auth()->id())
            ->where('type', USER_TYPE_DEALER)
            ->first();

        abort_unless($dealer, 403, 'Dealer access only.');

        return $dealer;
    }

    /**
     * Get active Agents under the logged-in Dealer.
     */
    private function getAgentIds(Partner $dealer)
    {
        return MarketingRelation::where('parent_type', USER_TYPE_DEALER)
            ->where('parent_id', $dealer->id)
            ->where('child_type', USER_TYPE_AGENT)
            ->where('status', 1)
            ->pluck('child_id');
    }

    /**
     * Display Agent orders belonging to this Dealer.
     */
    public function index(Request $request)
    {
        $dealer = $this->getDealer();

        $agentIds = $this->getAgentIds($dealer);

        // Validate filters
        $request->validate([
            'status' => [
                'nullable',
                'integer',
                Rule::in([
                    ORDER_STATUS_PENDING,
                    ORDER_STATUS_ACCEPTED,
                    ORDER_STATUS_ON_THE_WAY,
                    ORDER_STATUS_COMPLETED,
                    ORDER_STATUS_REJECTED,
                ]),
            ],

            'agent_id' => [
                'nullable',
                'integer',
                Rule::in($agentIds->map(fn($id) => (int) $id)->all()),
            ],

            'from_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'to_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:from_date',
            ],
        ]);

        // Only this Dealer's active Agents
        $agents = Partner::whereIn('id', $agentIds)
            ->where('type', USER_TYPE_AGENT)
            ->orderBy('name')
            ->get();

        // Only orders placed by this Dealer's Agents
        $query = Order::with([
            'userType',
            'partner',
            'items.product',
        ])
            ->where('user_type_id', USER_TYPE_AGENT)
            ->whereIn('reference_id', $agentIds);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Agent filter
        if ($request->filled('agent_id')) {
            $query->where('reference_id', $request->agent_id);
        }

        // From date filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // To date filter
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->latest()
            ->paginate(20)
            ->withQueryString();

        return view('partner.agent-orders.index', compact(
            'dealer',
            'agents',
            'orders'
        ));
    }

    /**
     * Display a single Agent order.
     */
    public function show($id)
    {
        $dealer = $this->getDealer();

        $agentIds = $this->getAgentIds($dealer);

        // Dealer can only view orders belonging to their own active Agents
        $order = Order::with([
            'userType',
            'partner',
            'items.product',
        ])
            ->where('id', $id)
            ->where('user_type_id', USER_TYPE_AGENT)
            ->whereIn('reference_id', $agentIds)
            ->firstOrFail();

        return view('partner.agent-orders.show', compact(
            'dealer',
            'order'
        ));
    }

    /**
     * Display incoming Admin-to-Dealer transfers.
     */
    public function incoming()
    {
        $dealer = \App\Models\Partner::where('user_id', auth()->id())
            ->where('type', USER_TYPE_DEALER)
            ->first();

        abort_unless($dealer, 403, 'Dealer access only.');

        $transfers = \App\Models\OrderTransfer::with([
            'order',
            'assignedEmployee',
            'items.orderItem.product',
        ])
            ->where('from_type', USER_TYPE_ADMIN)
            ->where('to_type', USER_TYPE_DEALER)
            ->where('to_id', $dealer->id)
            ->whereIn('status', [2, 3])
            ->latest()
            ->paginate(20);

        return view('partner.transfers.incoming', compact(
            'dealer',
            'transfers'
        ));
    }


    public function receive($id)
    {
        $dealer = $this->getDealer();

        \Illuminate\Support\Facades\DB::transaction(function () use ($id, $dealer) {

            $transfer = \App\Models\OrderTransfer::where('id', $id)
                ->where('from_type', USER_TYPE_ADMIN)
                ->where('to_type', USER_TYPE_DEALER)
                ->where('to_id', $dealer->id)
                ->lockForUpdate()
                ->firstOrFail();

            // শুধু Sent transfer Receive করা যাবে
            abort_unless(
                (int) $transfer->status === 2,
                422,
                'This transfer has already been received or cannot be received.'
            );

            $items = $transfer->items()
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                abort(422, 'This transfer has no products.');
            }

            // Transfer-এর সব পণ্য একসঙ্গে Receive হবে
            foreach ($items as $item) {
                $item->quantity_received = $item->quantity_sent;
                $item->save();
            }

            $transfer->status = 3;
            $transfer->received_at = now();
            $transfer->save();
        });

        return redirect()
            ->route('partner.incoming-transfers')
            ->with('success', 'Transfer received successfully.');
    }

    public function sendToAgent(Request $request, $id)
    {
        $dealer = $this->getDealer();

        $request->validate([
            'assigned_employee_id' => ['required', 'integer'],
        ]);

        $setting = \App\Models\Setting::where('slug', 'deliveryMan')
            ->where('status', 1)
            ->first();

        abort_unless(
            $setting && $setting->value,
            422,
            'Delivery Man designation is not configured in Settings.'
        );

        $deliveryMan = \App\Models\Employee::where(
            'id',
            $request->assigned_employee_id
        )
            ->where('designation_id', $setting->value)
            ->where('status', 1)
            ->first();

        abort_unless(
            $deliveryMan,
            422,
            'Please select a valid active Delivery Man.'
        );

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $id,
            $dealer,
            $deliveryMan
        ) {
            $sourceTransfer = \App\Models\OrderTransfer::where('id', $id)
                ->where('from_type', USER_TYPE_ADMIN)
                ->where('to_type', USER_TYPE_DEALER)
                ->where('to_id', $dealer->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                (int) $sourceTransfer->status === 3,
                422,
                'Only received transfers can be sent to the Agent.'
            );

            // Prevent duplicate forwarding
            $alreadyForwarded = \App\Models\OrderTransfer::where(
                'source_transfer_id',
                $sourceTransfer->id
            )->exists();

            abort_if(
                $alreadyForwarded,
                422,
                'This transfer has already been sent to the Agent.'
            );

            // Validate original Agent order
            $order = \App\Models\Order::where('id', $sourceTransfer->order_id)
                ->where('user_type_id', USER_TYPE_AGENT)
                ->firstOrFail();

            $agent = \App\Models\Partner::where(
                'id',
                $order->reference_id
            )
                ->where('type', USER_TYPE_AGENT)
                ->firstOrFail();

            // Ensure the Agent belongs to this Dealer
            $relationExists = \App\Models\MarketingRelation::where(
                'parent_type',
                USER_TYPE_DEALER
            )
                ->where('parent_id', $dealer->id)
                ->where('child_type', USER_TYPE_AGENT)
                ->where('child_id', $agent->id)
                ->where('status', 1)
                ->exists();

            abort_unless(
                $relationExists,
                403,
                'This Agent is not assigned to your Dealer account.'
            );

            $items = $sourceTransfer->items()
                ->lockForUpdate()
                ->get();

            abort_if(
                $items->isEmpty(),
                422,
                'This transfer has no products.'
            );

            foreach ($items as $item) {
                abort_if(
                    $item->quantity_received === null ||
                        (float) $item->quantity_received <= 0,
                    422,
                    'A product has no valid received quantity.'
                );
            }

            // Generate transfer number
            do {
                $transferNumber = 'DTA-'
                    . now()->format('YmdHis')
                    . '-'
                    . strtoupper(\Illuminate\Support\Str::random(5));
            } while (
                \App\Models\OrderTransfer::where(
                    'transfer_number',
                    $transferNumber
                )->exists()
            );

            // Create new Dealer -> Agent transfer
            $newTransfer = \App\Models\OrderTransfer::create([
                'transfer_number'     => $transferNumber,
                'order_id'            => $sourceTransfer->order_id,
                'source_transfer_id'  => $sourceTransfer->id,
                'from_type'           => USER_TYPE_DEALER,
                'from_id'             => $dealer->id,
                'to_type'             => USER_TYPE_AGENT,
                'to_id'               => $agent->id,
                'assigned_employee_id' => $deliveryMan->id,
                'status'              => 2,
                'sent_at'             => now(),
                'received_at'         => null,
                'note'                => 'Forwarded from Admin transfer '
                    . $sourceTransfer->transfer_number,
                'created_by'          => auth()->id(),
            ]);

            // Copy the exact received quantities
            foreach ($items as $item) {
                \App\Models\OrderTransferItem::create([
                    'order_transfer_id' => $newTransfer->id,
                    'order_item_id'     => $item->order_item_id,
                    'quantity_sent'     => $item->quantity_received,
                    'quantity_received' => null,
                ]);
            }
        });

        return redirect()
            ->route('partner.transfers.outgoing')
            ->with('success', 'Products sent to the Agent successfully.');
    }


    public function outgoing()
    {
        $dealer = $this->getDealer();

        $transfers = \App\Models\OrderTransfer::with([
            'order',
            'items.orderItem.product',
            'forwardedTransfer',
        ])
            ->where('from_type', USER_TYPE_ADMIN)
            ->where('to_type', USER_TYPE_DEALER)
            ->where('to_id', $dealer->id)
            ->where('status', 3)
            ->latest('received_at')
            ->paginate(20);

        $setting = \App\Models\Setting::where('slug', 'deliveryMan')
            ->where('status', 1)
            ->first();

        $deliveryMen = collect();

        if ($setting && $setting->value) {
            $deliveryMen = \App\Models\Employee::where(
                'designation_id',
                $setting->value
            )
                ->where('status', 1)
                ->orderBy('full_name')
                ->get();
        }

        return view('partner.transfers.outgoing', compact(
            'dealer',
            'transfers',
            'deliveryMen'
        ));
    }
}
