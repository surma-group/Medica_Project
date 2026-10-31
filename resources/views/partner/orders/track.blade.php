
@extends('layouts.partner')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">

            {{-- PAGE HEADER --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-truck"></i> Track Order
                    </h4>

                    <a href="{{ route('partner.orders') }}"
                       class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>

                <div class="card-body">

                    {{-- ORDER INFORMATION --}}
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted">Order Number</small>
                                <h5 class="mt-2 mb-0">
                                    {{ $order->order_number }}
                                </h5>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted">Order Date</small>
                                <h5 class="mt-2 mb-0">
                                    {{ $order->created_at
                                        ? $order->created_at->format('d M Y, h:i A')
                                        : 'N/A' }}
                                </h5>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted">Order Status</small>
                                <div class="mt-2">
                                    <span class="badge bg-primary">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ORDER TOTAL --}}
                    <div class="alert alert-light border">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Order Total</strong>
                            <h5 class="mb-0">
                                {{ number_format((float) $order->grand_total, 2) }}
                            </h5>
                        </div>
                    </div>

                    {{-- TRANSFER TRACKING --}}
                    <h5 class="mb-3">
                        <i class="fas fa-route"></i> Transfer History
                    </h5>

                    @if($transfers->isEmpty())

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            No transfer has been recorded for this order yet.
                        </div>

                    @else

                        @foreach($transfers as $transfer)

                            @php
                                $statusLabels = [
                                    0 => 'Pending',
                                    1 => 'Assigned',
                                    2 => 'Sent',
                                    3 => 'Received',
                                    4 => 'Cancelled',
                                ];

                                $statusColors = [
                                    0 => 'secondary',
                                    1 => 'info',
                                    2 => 'warning',
                                    3 => 'success',
                                    4 => 'danger',
                                ];

                                $isDealerToAgent =
                                    (int) $transfer->from_type === USER_TYPE_DEALER
                                    && (int) $transfer->to_type === USER_TYPE_AGENT;

                                $transferStatus = (int) $transfer->status;
                            @endphp

                            <div class="card border mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <strong>
                                            {{ $isDealerToAgent
                                                ? 'Dealer → Agent'
                                                : 'Admin → Dealer' }}
                                        </strong>

                                        <div class="small text-muted mt-1">
                                            Transfer No:
                                            {{ $transfer->transfer_number }}
                                        </div>
                                    </div>

                                    <span class="badge bg-{{ $statusColors[$transferStatus] ?? 'secondary' }}">
                                        {{ $statusLabels[$transferStatus] ?? 'Unknown' }}
                                    </span>
                                </div>

                                <div class="card-body">

                                    {{-- TRANSFER DATES --}}
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted">
                                                Sent At
                                            </small>
                                            <div>
                                                {{ $transfer->sent_at
                                                    ? \Carbon\Carbon::parse($transfer->sent_at)->format('d M Y, h:i A')
                                                    : 'Not sent yet' }}
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted">
                                                Received At
                                            </small>
                                            <div>
                                                {{ $transfer->received_at
                                                    ? \Carbon\Carbon::parse($transfer->received_at)->format('d M Y, h:i A')
                                                    : 'Not received yet' }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DELIVERY MAN --}}
                                    @if($isDealerToAgent)
                                        <div class="alert alert-light border mb-3">
                                            <strong>
                                                <i class="fas fa-user"></i>
                                                Delivery Man:
                                            </strong>

                                            @if($transfer->assignedEmployee)
                                                {{ $transfer->assignedEmployee->full_name }}
                                                @if($transfer->assignedEmployee->employee_code)
                                                    ({{ $transfer->assignedEmployee->employee_code }})
                                                @endif
                                            @else
                                                Not assigned
                                            @endif
                                        </div>
                                    @endif

                                    {{-- PRODUCTS --}}
                                    <h6 class="mb-2">Products</h6>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th class="text-end">Quantity Sent</th>
                                                    <th class="text-end">Quantity Received</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse($transfer->items as $transferItem)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>

                                                        <td>
                                                            {{ $transferItem->orderItem->product->name
                                                                ?? 'Product unavailable' }}
                                                        </td>

                                                        <td class="text-end">
                                                            {{ number_format((float) $transferItem->quantity_sent, 2) }}
                                                        </td>

                                                        <td class="text-end">
                                                            @if($transferItem->quantity_received !== null)
                                                                {{ number_format((float) $transferItem->quantity_received, 2) }}
                                                            @else
                                                                <span class="text-muted">Pending</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">
                                                            No products found in this transfer.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- NOTES --}}
                                    @if($transfer->note)
                                        <div class="mt-3">
                                            <strong>Note:</strong>
                                            <p class="mb-0">
                                                {{ $transfer->note }}
                                            </p>
                                        </div>
                                    @endif

                                </div>
                            </div>

                        @endforeach

                    @endif

                    {{-- ORDER ITEMS --}}
                    <h5 class="mt-4 mb-3">
                        <i class="fas fa-boxes"></i> Original Order Products
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-end">Order Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($order->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>
                                            {{ $item->product->name ?? 'Product unavailable' }}
                                        </td>

                                        <td class="text-end">
                                            {{ number_format((float) $item->quantity, 2) }}
                                        </td>

                                        <td class="text-end">
                                            {{ number_format((float) $item->price, 2) }}
                                        </td>

                                        <td class="text-end">
                                            {{ number_format((float) $item->total, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            No order items found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">
                                        Grand Total
                                    </th>
                                    <th class="text-end">
                                        {{ number_format((float) $order->grand_total, 2) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
