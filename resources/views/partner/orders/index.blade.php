@extends('layouts.partner')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Orders</h4>

                    <a href="{{ route('partner.orders.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Create Order
                    </a>
                </div>

                {{-- SUCCESS / ERROR MESSAGE --}}
                <div class="px-3 pt-3">

                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                </div>

                <form action="{{ route('partner.orders') }}" method="GET" class="row g-3 mb-4">

                    <div class="col-md-3">
                        <label for="from_date" class="form-label">From Date</label>
                        <input
                            type="date"
                            name="from_date"
                            id="from_date"
                            class="form-control"
                            value="{{ request('from_date', now()->subMonth()->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="to_date" class="form-label">To Date</label>
                        <input
                            type="date"
                            name="to_date"
                            id="to_date"
                            class="form-control"
                            value="{{ request('to_date', now()->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label">Order Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="pending"
                                {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>
                                Pending Orders
                            </option>

                            <option value="completed"
                                {{ request('status') == 'completed' ? 'selected' : '' }}>
                                Completed Orders
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Search
                        </button>

                        <a href="{{ route('partner.orders') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </form>


                {{-- TABLE --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order No</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $order->order_number }}</td>

                                    <td>
                                        {{ $order->customer->name ?? 'N/A' }} <br>
                                        <small>{{ $order->customer->phone ?? '' }}</small>
                                    </td>

                                    <td>
                                        {{ number_format($order->grand_total, 2) }}
                                    </td>

                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $order->created_at->format('d M Y') }}
                                    </td>


                                    <td class="text-end">

                                        {{-- View Order --}}
                                        <a href="{{ route('partner.orders.show', $order->id) }}"
                                            class="btn btn-sm btn-info">
                                            View
                                        </a>

                                        {{-- Track Order --}}
                                        <a href="{{ route('partner.orders.track', $order->id) }}"
                                            class="btn btn-sm btn-success">
                                            <i class="fas fa-truck"></i> Track Order
                                        </a>

                                        @php
                                        $orderTransfers = $agentTransfers->get($order->id, collect());
                                        @endphp



                                        {{-- Receive Transfer Button --}}
                                        @php
                                        $orderTransfers = $agentTransfers->get($order->id, collect());
                                        @endphp

                                        @foreach($orderTransfers as $transfer)
                                        @if((int) $transfer->status === 2)
                                        <button type="button"
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#receiveTransferModal"

                                            data-id="{{ $transfer->id }}"
                                            data-number="{{ $transfer->transfer_number }}"
                                            data-amount="{{ number_format((float) $order->grand_total, 2, '.', '') }}"

                                            data-name="{{ $transfer->assignedEmployee->full_name ?? '' }}"
                                            data-code="{{ $transfer->assignedEmployee->employee_code ?? '' }}"
                                            data-account="{{ $transfer->assignedEmployee->account_number ?? '' }}">

                                            <i class="fas fa-box-open"></i> Receive

                                        </button>
                                        @elseif((int) $transfer->status === 3)
                                        <span class="badge bg-success">Received</span>
                                        @endif
                                        @endforeach



                                        {{-- Edit Order --}}
                                        @if($order->status == ORDER_STATUS_PENDING)
                                        <a href="{{ route('partner.orders.edit', $order->id) }}"
                                            class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        @endif

                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        No orders found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- Common Receive Transfer Modal --}}
<div class="modal fade" id="receiveTransferModal" tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-box-open text-primary"></i>
                    Receive Order
                </h5>

                <button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <form id="receiveTransferForm" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- Transfer Number --}}
                    <div class="mb-3">
                        <label class="form-label">Transfer Number</label>

                        <input type="text"
                            id="receiveTransferNumber"
                            class="form-control"
                            readonly>
                    </div>

                    {{-- Assigned Delivery Man Information --}}
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-user text-primary"></i>
                            Assigned Delivery Man
                        </label>

                        <div id="deliveryManInfo"
                            class="border rounded p-3"
                            style="background: #f8f9fa;">

                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-circle text-primary me-2"
                                    style="font-size: 22px;"></i>

                                <strong id="deliveryManName">-</strong>
                            </div>

                            <div class="mb-1">
                                <small class="text-muted">Account Number:</small>
                                <span id="deliveryManAccount">-</span>
                            </div>

                        </div>
                    </div>

                    {{-- Order Amount --}}
                    <div class="mb-3">
                        <label class="form-label">Order Amount</label>

                        <div class="input-group">
                            <span class="input-group-text">৳</span>

                            <input type="number"
                                id="receiveTransferAmount"
                                class="form-control"
                                readonly>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i>
                        Confirm করলে এই Transfer-এর সব পণ্য Received হবে।
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i>
                        Confirm Receive
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const modal = document.getElementById('receiveTransferModal');
        const form = document.getElementById('receiveTransferForm');

        if (!modal || !form) return;

        modal.addEventListener('show.bs.modal', function(event) {

            const button = event.relatedTarget;

            const transferId = button.dataset.id;

            // Transfer Number
            document.getElementById('receiveTransferNumber').value =
                button.dataset.number || '';

            // Order Amount
            document.getElementById('receiveTransferAmount').value =
                button.dataset.amount || '';

            // Assigned Delivery Man
            document.getElementById('deliveryManName').textContent =
                button.dataset.name || 'Not Assigned';

            document.getElementById('deliveryManAccount').textContent =
                button.dataset.account || '-';

            // Receive Form Action
            const urlTemplate = @json(route('partner.orders.receive-transfer', ['id' => '__ID__']));

            form.action = urlTemplate.replace('__ID__', transferId);

        });

    });
</script>
@endpush