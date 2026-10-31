@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">

            <div class="card">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Orders</h4>

                    <a href="{{ route('orders.index') }}" class="btn btn-primary">
                        All Orders
                    </a>
                </div>

                {{-- TABLE --}}
                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-striped table-md">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order No</th>
                                    <th>User Type</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th style="width:150px;">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($orders as $order)

                                @php

                                $name = '-';
                                $phone = '-';

                                if($order->user_type_id == USER_TYPE_CUSTOMER){

                                $name = $order->customer->name ?? '-';
                                $phone = $order->customer->phone ?? '-';

                                } else {

                                $name =
                                $order->partner->shop_name
                                ??
                                $order->partner->name
                                ??
                                '-';

                                $phone =
                                $order->partner->phone
                                ??
                                '-';
                                }

                                @endphp

                                <tr>

                                    <td>
                                        {{ $orders->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        {{ $order->order_number }}
                                    </td>

                                    <td>

                                        @if($order->user_type_id == USER_TYPE_CUSTOMER)

                                        <span class="badge bg-success">
                                            Customer
                                        </span>

                                        @elseif($order->user_type_id == USER_TYPE_DEALER)

                                        <span class="badge bg-primary">
                                            Dealer
                                        </span>

                                        @elseif($order->user_type_id == USER_TYPE_AGENT)

                                        <span class="badge bg-info">
                                            Agent
                                        </span>

                                        @else

                                        <span class="badge bg-secondary">
                                            Unknown
                                        </span>

                                        @endif

                                    </td>

                                    <td>
                                        {{ $name }}
                                    </td>

                                    <td>
                                        {{ $phone }}
                                    </td>

                                    <td>
                                        {{ number_format($order->grand_total, 2) }}
                                    </td>

                                    <td>

                                        @if($order->status == ORDER_STATUS_PENDING)

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                        @elseif($order->status == ORDER_STATUS_COMPLETED)

                                        <span class="badge bg-success text-white">
                                            Completed
                                        </span>

                                        @elseif($order->status == ORDER_STATUS_ACCEPTED)

                                        <span class="badge bg-primary text-white">
                                            Accepted
                                        </span>

                                        @elseif($order->status == ORDER_STATUS_ON_THE_WAY)

                                        <span class="badge bg-info text-white">
                                            On The Way
                                        </span>

                                        @elseif($order->status == ORDER_STATUS_REJECTED)

                                        <span class="badge bg-danger text-white">
                                            Cancelled
                                        </span>

                                        @else

                                        <span class="badge bg-secondary">
                                            Unknown
                                        </span>

                                        @endif

                                    </td>

                                    <td>
                                        {{ $order->created_at->format('d M, Y') }}
                                    </td>

                                    <td>

                                        <a href="{{ route('orders.show', $order->id) }}"
                                            class="btn btn-sm btn-info">
                                            View
                                        </a>

                                        {{-- CANCEL BUTTON --}}
                                        @if($order->status == ORDER_STATUS_PENDING || $order->status == ORDER_STATUS_ACCEPTED)
                                        <form action="{{ route('orders.update-status', $order->id) }}"
                                            method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Cancel this order?');">

                                            @csrf

                                            <input type="hidden" name="status" value="{{ ORDER_STATUS_REJECTED }}">

                                            <button class="btn btn-sm btn-warning">
                                                Cancel
                                            </button>

                                        </form>
                                        @endif

                                        {{-- DELETE BUTTON --}}
                                        <form action="{{ route('orders.destroy', $order->id) }}"
                                            method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Are you sure?');">

                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger">
                                                Delete
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="9" class="text-center">
                                        No orders found.
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                {{-- PAGINATION --}}
                <div class="card-footer text-end">
                    {{ $orders->links() }}
                </div>

            </div>

        </div>
    </div>
</section>
@endsection