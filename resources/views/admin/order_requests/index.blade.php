@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Order Requests</h4>

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
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th style="width:150px;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $order->order_number }}</td>

                                    <td>
                                        {{ $order->customer->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $order->customer->phone ?? '-' }}
                                    </td>

                                    <td>
                                        @if($order->type == 1)
                                        <span class="badge bg-primary text-white">General</span>
                                        @else
                                        <span class="badge bg-info text-white">Prescription</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($order->status == 0)
                                        <span class="badge bg-warning text-dark">Pending</span>

                                        @elseif($order->status == 1)
                                        <span class="badge bg-success text-white">Approved</span>

                                        @else
                                        <span class="badge bg-danger text-white">Rejected</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $order->created_at->format('d M, Y') }}
                                    </td>

                                    <td>
                                        <a href="{{ route('order-requests.show', $order->id) }}"
                                            class="btn btn-sm btn-info">
                                            View
                                        </a>

                                        {{-- optional delete --}}
                                        <form action="{{ route('order-requests.destroy', $order->id) }}"
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
                                    <td colspan="8" class="text-center">
                                        No order requests found.
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