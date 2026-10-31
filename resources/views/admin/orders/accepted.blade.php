@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">

            <div class="card">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">

                    <h4 class="mb-0">Accepted Orders</h4>

                    {{-- RIGHT SIDE BUTTONS --}}
                    <div class="d-flex gap-2">
                        <a href="{{ route('orders.index') }}" class="btn btn-primary">
                            All Orders
                        </a>

                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#medicineModal">
                            Medicine List
                        </button>
                    </div>

                </div>

                {{-- FILTER FORM --}}
                <div class="card-body">

                    <form method="GET" action="{{ route('orders.accepted') }}" class="row g-2 mb-3">

                        {{-- From Date --}}
                        <div class="col-md-3">
                            <input type="date" name="from_date" class="form-control"
                                value="{{ request('from_date', now()->subDays(7)->toDateString()) }}">
                        </div>

                        {{-- To Date --}}
                        <div class="col-md-3">
                            <input type="date" name="to_date" class="form-control"
                                value="{{ request('to_date', now()->toDateString()) }}">
                        </div>

                        {{-- User Type --}}
                        <div class="col-md-3">
                            <select name="user_type" class="form-control">
                                <option value="">All User Types</option>

                                @foreach($userTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ request('user_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Filter Button --}}
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100">
                                Filter
                            </button>
                        </div>

                    </form>

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
                                    <th>Transfer Status</th>
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
                                $name = $order->partner->shop_name
                                ?? $order->partner->name
                                ?? '-';

                                $phone = $order->partner->phone ?? '-';
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
                                        <span class="badge bg-success">Customer</span>

                                        @elseif($order->user_type_id == USER_TYPE_DEALER)
                                        <span class="badge bg-primary">Dealer</span>

                                        @elseif($order->user_type_id == USER_TYPE_AGENT)
                                        <span class="badge bg-info">Agent</span>

                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif

                                    </td>

                                    <td>{{ $name }}</td>
                                    <td>{{ $phone }}</td>

                                    <td>
                                        {{ number_format($order->grand_total, 2) }}
                                    </td>

                                    <td>
                                        <span class="badge bg-primary text-white">
                                            Accepted
                                        </span>
                                    </td>

                                    <td>
                                        {{ $order->created_at->format('d M, Y') }}
                                    </td>
                                    <td>
                                        @if($order->transfer_status_label === 'Not Assigned')
                                        <span class="badge bg-secondary">Not Assigned</span>
                                        @elseif($order->transfer_status_label === 'Partially Sent')
                                        <span class="badge bg-warning text-dark">Partially Sent</span>
                                        @else
                                        <span class="badge bg-success">Fully Sent</span>
                                        @endif

                                        <div class="small text-muted mt-1">
                                            Sent: {{ number_format($order->transferred_qty_total, 3) }}
                                            /
                                            {{ number_format($order->ordered_qty_total, 3) }}
                                        </div>
                                    </td>
                                    <td>

                                        <a href="{{ route('orders.show', $order->id) }}"
                                            class="btn btn-sm btn-info">
                                            View
                                        </a>
                                        @if($order->user_type_id == USER_TYPE_AGENT)

                                        @if($order->remaining_qty_total > 0.000001)

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-success assign-order-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#assignOrderModal"
                                            data-order-id="{{ $order->id }}"
                                            data-order-number="{{ $order->order_number }}"
                                            data-items-url="{{ route('orders.transfer-items', $order->id) }}">

                                            <i class="fas fa-truck"></i>

                                            @if($order->transferred_qty_total > 0)
                                            Assign Remaining
                                            @else
                                            Assign
                                            @endif
                                        </button>

                                        @else

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-secondary"
                                            disabled>
                                            <i class="fas fa-check"></i> Fully Sent
                                        </button>

                                        @endif

                                        @endif
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

                                    </td>

                                </tr>

                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        No accepted orders found.
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
<div class="modal fade" id="medicineModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h5 class="modal-title">Medicine Summary</h5>

                <button class="btn btn-outline-dark btn-sm"
                    onclick="window.print()">
                    Print
                </button>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Medicine Name</th>
                            <th>Strength</th>
                            <th>Company</th>
                            <th>Total Qty</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($medicineList as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>

                            <td>
                                {{ $item->medicine_name }}
                            </td>

                            <td>
                                {{ $item->strength ?? '-' }}
                            </td>

                            <td>
                                {{ $item->company_name ?? '-' }}
                            </td>

                            <td>
                                {{ $item->total_qty }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No medicine found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

        </div>
    </div>
</div>
{{-- Common Assign Order Modal --}}
<div
    class="modal fade"
    id="assignOrderModal"
    tabindex="-1"
    aria-labelledby="assignOrderModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form
                action="{{ route('order-transfers.store') }}"
                method="POST"
                id="assignOrderForm">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="assignOrderModalLabel">
                        Assign Transit Officer & Send Products
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>

                <div class="modal-body">

                    {{-- Order ID --}}
                    <input
                        type="hidden"
                        name="order_id"
                        id="assign_order_id">

                    {{-- Order Information --}}
                    <div class="alert alert-info">
                        Order Number:
                        <strong id="assign_order_number"></strong>
                    </div>

                    {{-- Transit Officer --}}
                    <div class="mb-3">
                        <label for="transit_officer_id" class="form-label">
                            Select Transit Officer
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="assigned_employee_id"
                            id="transit_officer_id"
                            class="form-select"
                            required>

                            <option value="">
                                -- Select Transit Officer --
                            </option>

                            @foreach($transitOfficers as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->full_name }}
                                @if($employee->employee_code)
                                ({{ $employee->employee_code }})
                                @endif
                            </option>
                            @endforeach

                        </select>

                        @if($transitOfficers->isEmpty())
                        <small class="text-danger">
                            No active Transit Officer found.
                            Please check the Transit Officer setting
                            and employee designation.
                        </small>
                        @endif
                    </div>

                    {{-- Order Items --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">

                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Order Qty</th>
                                    <th>Send Qty</th>
                                </tr>
                            </thead>

                            <tbody id="assignOrderItems">
                                <tr>
                                    <td colspan="3" class="text-center">
                                        Select an order to view products.
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                    {{-- Note --}}
                    <div class="mb-3">
                        <label for="transfer_note" class="form-label">
                            Note (Optional)
                        </label>

                        <textarea
                            name="note"
                            id="transfer_note"
                            class="form-control"
                            rows="3"
                            placeholder="Enter transfer note">
                        </textarea>
                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>

                    <button
                        type="submit"
                        class="btn btn-success"
                        {{ $transitOfficers->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane"></i>
                        Assign & Send to Dealer
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

        const modal = document.getElementById('assignOrderModal');
        const form = document.getElementById('assignOrderForm');
        const orderIdInput = document.getElementById('assign_order_id');
        const orderNumber = document.getElementById('assign_order_number');
        const itemsBody = document.getElementById('assignOrderItems');
        const officerSelect = document.getElementById('transit_officer_id');
        const noteInput = document.getElementById('transfer_note');
        const submitButton = form.querySelector('button[type="submit"]');

        let requestNumber = 0;

        function showMessage(message) {
            itemsBody.replaceChildren();

            const row = document.createElement('tr');
            const cell = document.createElement('td');

            cell.colSpan = 3;
            cell.className = 'text-center';
            cell.textContent = message;

            row.appendChild(cell);
            itemsBody.appendChild(row);
        }

        document.querySelectorAll('.assign-order-btn').forEach(function(button) {

            button.addEventListener('click', async function() {

                const currentRequest = ++requestNumber;
                const orderId = this.dataset.orderId;
                const url = this.dataset.itemsUrl;

                orderIdInput.value = orderId;
                orderNumber.textContent = this.dataset.orderNumber;

                officerSelect.value = '';
                noteInput.value = '';

                submitButton.disabled = true;
                showMessage('Loading order products...');

                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load order products.');
                    }

                    const data = await response.json();

                    // Ignore an older request if another order was selected.
                    if (currentRequest !== requestNumber) {
                        return;
                    }

                    itemsBody.replaceChildren();

                    if (!data.items || data.items.length === 0) {
                        showMessage('No products found in this order.');
                        return;
                    }

                    data.items.forEach(function(item) {

                        const row = document.createElement('tr');

                        // Product name
                        const productCell = document.createElement('td');
                        productCell.textContent = item.product_name;

                        if (item.strength) {
                            productCell.textContent += ' (' + item.strength + ')';
                        }

                        // Ordered quantity
                        const quantityCell = document.createElement('td');
                        quantityCell.textContent = item.quantity;

                        // Send quantity input
                        const sendCell = document.createElement('td');
                        const input = document.createElement('input');

                        input.type = 'number';
                        input.name = `items[${item.id}][quantity_sent]`;
                        input.className = 'form-control form-control-sm';
                        input.min = '0.001';
                        input.max = item.quantity;
                        input.step = '0.001';
                        input.required = true;
                        input.value = item.quantity;

                        // Include the order item ID in the submitted form.
                        const hidden = document.createElement('input');

                        hidden.type = 'hidden';
                        hidden.name = `items[${item.id}][order_item_id]`;
                        hidden.value = item.id;

                        sendCell.appendChild(input);
                        sendCell.appendChild(hidden);

                        row.appendChild(productCell);
                        row.appendChild(quantityCell);
                        row.appendChild(sendCell);

                        itemsBody.appendChild(row);
                    });

                } catch (error) {
                    if (currentRequest === requestNumber) {
                        showMessage(
                            'Could not load products. Please close the modal and try again.'
                        );
                        console.error(error);
                    }
                } finally {
                    if (currentRequest === requestNumber) {
                        submitButton.disabled = false;
                    }
                }
            });
        });

        // Prevent sending invalid quantities.
        form.addEventListener('submit', function(event) {

            const quantityInputs = itemsBody.querySelectorAll(
                'input[name$="[quantity_sent]"]'
            );

            if (quantityInputs.length === 0) {
                event.preventDefault();
                alert('Please load the order products first.');
                return;
            }

            for (const input of quantityInputs) {
                const quantity = Number(input.value);
                const max = Number(input.max);

                if (
                    !Number.isFinite(quantity) ||
                    quantity <= 0 ||
                    quantity > max
                ) {
                    event.preventDefault();
                    alert('Send quantity must be greater than 0 and cannot exceed the order quantity.');
                    input.focus();
                    return;
                }
            }
        });

        modal.addEventListener('hidden.bs.modal', function() {
            requestNumber++;
        });

    });
</script>
@endpush

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #medicineModal,
        #medicineModal * {
            visibility: visible;
        }

        #medicineModal {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .btn,
        .modal-header button {
            display: none !important;
        }
    }
</style>
@endpush