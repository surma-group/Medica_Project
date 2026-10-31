@extends('layouts.website')

@section('title', 'My Orders - Medica')

@section('content')

<!-- Page Header -->
<div class="container-fluid page-header py-5">
    <div class="container text-center py-5">

        <h1 class="text-white display-6">My Orders</h1>

        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Home</a>
            </li>

            <li class="breadcrumb-item">
                <a href="{{ route('profile') }}">Profile</a>
            </li>

            <li class="breadcrumb-item active text-white">
                My Orders
            </li>
        </ol>

    </div>
</div>

<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row g-4">

            <!-- LEFT SIDEBAR -->
            <div class="col-lg-3">
                <div class="bg-light rounded p-4">

                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                        <h5 class="mt-3">{{ auth()->user()->name }}</h5>
                    </div>

                    <div class="list-group">

                        <a href="{{ route('profile') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>

                        <a href="{{ route('profile.edit') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-edit me-2"></i> Edit Profile
                        </a>

                        <a href="{{ route('profile.orders') }}"
                            class="list-group-item list-group-item-action active">
                            <i class="fas fa-box me-2"></i> My Orders
                        </a>

                        <a href="{{ route('logout') }}"
                            class="list-group-item list-group-item-action text-danger"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>

                    </div>

                </div>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="col-lg-9">

                <div class="bg-light rounded p-4">

                    <h4 class="mb-4">My Orders</h4>

                    @if(isset($orders) && count($orders) > 0)

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Order No</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>

                                    <td>
                                        {{ $order->created_at->format('d M, Y') }}
                                    </td>

                                    <td>
                                        <strong>{{ number_format($order->grand_total, 2) }} ৳</strong>
                                    </td>

                                    <td>
                                        @if($order->status == 0)
                                        <span class="badge bg-warning text-dark">Pending</span>

                                        @elseif($order->status == 2)
                                        <span class="badge bg-info text-white">Processing</span>

                                        @elseif($order->status == 1)
                                        <span class="badge bg-success text-white">Completed</span>

                                        @elseif($order->status == 3)
                                        <span class="badge bg-danger text-white">Cancelled</span>

                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        <a href="{{ route('profile.order.show', $order->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No orders found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                        <h5>No Orders Found</h5>
                        <p class="text-muted">You haven't placed any orders yet.</p>
                        <a href="{{ route('shop') }}" class="btn btn-primary">
                            Start Shopping
                        </a>
                    </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
</div>

@endsection