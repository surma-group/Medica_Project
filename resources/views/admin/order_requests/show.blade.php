@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Order #{{ $order->order_number }}</h4>

                    <a href="{{ route('order-requests.index') }}" class="btn btn-primary">
                        Back
                    </a>
                </div>

                <div class="card-body">

                    {{-- CUSTOMER + ORDER INFO --}}
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
                                    <b>Type:</b>
                                    {{ $order->type == 1 ? 'General' : 'Prescription' }}
                                </p>

                                <p class="mb-2">
                                    <b>Status:</b>

                                    @if($order->status == 0)
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($order->status == 1)
                                        <span class="badge bg-success text-white">Approved</span>
                                    @else
                                        <span class="badge bg-danger text-white">Rejected</span>
                                    @endif
                                </p>

                            </div>
                        </div>

                    </div>

                    {{-- PRESCRIPTION FILE --}}
                    @if($order->prescription_file)
                        <div class="mb-4">
                            <div class="border p-3 rounded">
                                <h6><b>Prescription File</b></h6>

                                <a href="{{ asset('storage/' . $order->prescription_file) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-info">
                                    View File
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- DESCRIPTION --}}
                    @if($order->prescription_description)
                        <div class="mb-4">
                            <div class="border p-3 rounded">
                                <h6><b>Description</b></h6>
                                <p class="mb-0">{{ $order->prescription_description }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- ITEMS TABLE (ONLY FOR GENERAL TYPE) --}}
                    @if($order->type == 1 && $order->items->count())
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Strength</th>
                                    <th>Company</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($order->items as $item)
                                <tr>

                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <b>{{ $item->product_name }}</b>
                                    </td>

                                    <td>{{ $item->strength ?? '-' }}</td>

                                    <td>{{ $item->company ?? '-' }}</td>

                                    <td>{{ $item->unit_id ?? '-' }}</td>

                                    <td>{{ $item->quantity }}</td>

                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</section>
@endsection