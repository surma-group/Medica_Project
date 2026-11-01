@extends('layouts.employee')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- Card Header --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Delivery Order Details</h4>

                    <a href="{{ route('employee.delivery-orders.index') }}"
                       class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card-body">

                    {{-- Transfer Information --}}
                    <div class="row mb-4">

                        <div class="col-md-6 mb-3">
                            <strong>Transfer Number:</strong>
                            <div>{{ $transfer->transfer_number }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Order Number:</strong>
                            <div>{{ $transfer->order->order_number ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Agent Name:</strong>
                            <div>{{ $agent->name ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Delivery Man:</strong>
                            <div>{{ $employee->full_name }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Assigned Date:</strong>
                            <div>
                                {{ $transfer->sent_at
                                    ? \Carbon\Carbon::parse($transfer->sent_at)->format('d M Y h:i A')
                                    : $transfer->created_at->format('d M Y h:i A') }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Received Date:</strong>
                            <div>
                                {{ $transfer->received_at
                                    ? \Carbon\Carbon::parse($transfer->received_at)->format('d M Y h:i A')
                                    : 'Not Received Yet' }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong>
                            <div>
                                @if($transfer->status == 2)
                                    <span class="badge badge-warning">
                                        Assigned
                                    </span>
                                @elseif($transfer->status == 3)
                                    <span class="badge badge-success">
                                        Delivered
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>

                    <hr>

                    {{-- Product Details --}}
                    <h5 class="mb-3">Product Details</h5>

                    <div class="table-responsive">
                        <table class="table table-striped table-md">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($transfer->items as $item)

                                    @php
                                        $quantity = (float) $item->quantity_sent;
                                        $price = (float) ($item->orderItem->price ?? 0);
                                        $total = $quantity * $price;
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>
                                            {{ $item->orderItem->product->name ?? 'N/A' }}
                                        </td>

                                        <td>
                                            {{ number_format($quantity, 3) }}
                                        </td>

                                        <td>
                                            {{ number_format($price, 2) }}
                                        </td>

                                        <td>
                                            {{ number_format($total, 2) }}
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="5" class="text-center">
                                            No products found.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                            {{-- Total Amount --}}
                            <tfoot>
                                <tr class="fw-bold table-light">
                                    <td colspan="4" class="text-end">
                                        Total Transfer Amount
                                    </td>

                                    <td>
                                        {{ number_format($transfer->transfer_amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                    {{-- Transfer Note --}}
                    @if($transfer->note)
                        <div class="mt-4">
                            <strong>Note:</strong>
                            <p class="mb-0">{{ $transfer->note }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</section>
@endsection