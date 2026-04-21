@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Order #{{ $order->order_number }}</h4>

                    <a href="{{ route('orders.index') }}" class="btn btn-primary">
                        Back
                    </a>
                </div>

                <div class="card-body">

                    {{-- CUSTOMER + ORDER INFO (EQUAL BOX) --}}
                    <div class="row mb-4">

                        {{-- CUSTOMER --}}
                        <div class="col-md-6 d-flex">
                            <div class="border p-3 rounded w-100 h-100">

                                <h6 class="mb-3"><b>Customer Information</b></h6>

                                <p class="mb-1"><b>Name:</b> {{ $order->customer->name ?? '-' }}</p>
                                <p class="mb-1"><b>Phone:</b> {{ $order->customer->phone ?? '-' }}</p>
                                <p class="mb-1"><b>Email:</b> {{ $order->customer->email ?? '-' }}</p>
                                <p class="mb-0"><b>Address:</b> {{ $order->customer->address ?? '-' }}</p>

                            </div>
                        </div>

                        {{-- ORDER --}}
                        <div class="col-md-6 d-flex">
                            <div class="border p-3 rounded w-100 h-100 text-md-end">

                                <h6 class="mb-3"><b>Order Information</b></h6>

                                <p class="mb-1">
                                    <b>Date:</b> {{ $order->created_at->format('d M, Y') }}
                                </p>

                                <p class="mb-1">
                                    <b>Payment:</b>
                                    {{ $order->payment_method == 1 ? 'Cash' : 'Online' }}
                                </p>

                                <p class="mb-2">
                                    <b>Status:</b>

                                    @if($order->status == 0)
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($order->status == 1)
                                        <span class="badge bg-primary text-white">Processing</span>
                                    @elseif($order->status == 2)
                                        <span class="badge bg-success text-white">Delivered</span>
                                    @else
                                        <span class="badge bg-danger text-white">Cancelled</span>
                                    @endif
                                </p>

                            </div>
                        </div>

                    </div>

                    {{-- PRODUCT TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Image</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($order->items as $item)
                                <tr>

                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <b>{{ $item->product->name ?? 'Deleted Product' }}</b>
                                    </td>

                                    <td>
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset($item->product->image) }}"
                                                 width="50"
                                                 height="50"
                                                 style="object-fit: cover; border-radius: 6px;">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>

                                    <td>{{ number_format($item->price, 2) }}</td>

                                    <td>{{ $item->quantity }}</td>

                                    <td>
                                        <b>{{ number_format($item->total, 2) }}</b>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    {{-- TOTAL SECTION --}}
                    <div class="row mt-4">
                        <div class="col-md-6"></div>

                        <div class="col-md-6">
                            <div class="border p-3 rounded">

                                <div class="d-flex justify-content-between">
                                    <span>Subtotal</span>
                                    <span>{{ number_format($order->subtotal, 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <span>Delivery</span>
                                    <span>{{ number_format($order->delivery_charge, 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <span>Discount</span>
                                    <span>-{{ number_format($order->discount, 2) }}</span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between">
                                    <h5><b>Grand Total</b></h5>
                                    <h5><b>{{ number_format($order->grand_total, 2) }}</b></h5>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>
@endsection