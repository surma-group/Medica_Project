@extends('layouts.partner')

@section('title', 'Send to Agent')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Send to Agent</h4>
            <p class="text-muted mb-0 small">
                Receive করা Order-এর পণ্য সংশ্লিষ্ট Agent-কে পাঠান।
                Send করার সময় Delivery Man নির্বাচন করুন।
            </p>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success py-2 px-3 small">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger py-2 px-3 small">
            {{ session('error') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger py-2 px-3 small">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Transfers Table --}}
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Received Orders</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Transfer No.</th>
                            <th>Order No.</th>
                            <th>Agent</th>
                            <th>Products</th>
                            <th>Total Qty</th>
                            <th>Received Date</th>
                            <th>Status</th>
                            <th width="190">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transfers as $transfer)

                            @php
                                $totalQuantity = $transfer->items->sum(
                                    fn ($item) => (float) $item->quantity_received
                                );

                                $agent = \App\Models\Partner::where(
                                    'id',
                                    $transfer->order->reference_id ?? 0
                                )
                                    ->where('type', USER_TYPE_AGENT)
                                    ->first();
                            @endphp

                            <tr>
                                {{-- Serial --}}
                                <td>
                                    {{ $transfers->firstItem() + $loop->index }}
                                </td>

                                {{-- Transfer Number --}}
                                <td>
                                    <strong>
                                        {{ $transfer->transfer_number }}
                                    </strong>
                                </td>

                                {{-- Order Number --}}
                                <td>
                                    {{ $transfer->order->order_number
                                        ?? $transfer->order_id }}
                                </td>

                                {{-- Agent --}}
                                <td>
                                    @if($agent)
                                        {{ $agent->shop_name
                                            ?: $agent->name
                                            ?: 'Agent' }}
                                    @else
                                        <span class="text-muted">
                                            Agent not found
                                        </span>
                                    @endif
                                </td>

                                {{-- Product Count --}}
                                <td>
                                    {{ $transfer->items->count() }}
                                </td>

                                {{-- Total Quantity --}}
                                <td>
                                    {{ number_format($totalQuantity, 2) }}
                                </td>

                                {{-- Received Date --}}
                                <td>
                                    {{ $transfer->received_at
                                        ? $transfer->received_at->format('d M Y, h:i A')
                                        : '—' }}
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($transfer->forwardedTransfer)
                                        <span class="badge bg-success">
                                            Sent
                                        </span>
                                    @elseif($transfer->status == 3)
                                        <span class="badge bg-primary">
                                            Ready to Send
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Not Available
                                        </span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td>
                                    @if($transfer->status == 3 && !$transfer->forwardedTransfer)

                                        @if($deliveryMen->isNotEmpty())

                                            <form
                                                action="{{ route(
                                                    'partner.transfers.send-to-agent',
                                                    $transfer->id
                                                ) }}"
                                                method="POST"
                                                onsubmit="return confirm('নির্বাচিত Delivery Man-কে assign করে পণ্যগুলো সংশ্লিষ্ট Agent-কে পাঠাতে চান?');"
                                            >
                                                @csrf

                                                <div class="mb-2">
                                                    <select
                                                        name="assigned_employee_id"
                                                        class="form-select form-select-sm"
                                                        required
                                                    >
                                                        <option value="">
                                                            Select Delivery Man
                                                        </option>

                                                        @foreach($deliveryMen as $employee)
                                                            <option
                                                                value="{{ $employee->id }}"
                                                                @selected(
                                                                    old('assigned_employee_id') == $employee->id
                                                                )
                                                            >
                                                                {{ $employee->full_name }}
                                                                @if($employee->employee_code)
                                                                    ({{ $employee->employee_code }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <button
                                                    type="submit"
                                                    class="btn btn-success btn-sm"
                                                >
                                                    <i
                                                        data-feather="send"
                                                        style="width:13px;height:13px;"
                                                    ></i>
                                                    Send
                                                </button>
                                            </form>

                                        @else
                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm"
                                                disabled
                                            >
                                                No Delivery Man
                                            </button>

                                            <small class="text-danger d-block mt-1">
                                                Settings-এ Delivery Man designation
                                                যাচাই করুন।
                                            </small>
                                        @endif

                                    @elseif($transfer->forwardedTransfer)

                                        <span class="text-success small">
                                            <i
                                                data-feather="check-circle"
                                                style="width:13px;height:13px;"
                                            ></i>
                                            Already Sent
                                        </span>

                                        <div class="small text-muted mt-1">
                                            {{ $transfer->forwardedTransfer->transfer_number }}
                                        </div>

                                    @else
                                        <span class="text-muted small">
                                            Not Available
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            {{-- Product Details --}}
                            <tr>
                                <td colspan="9" class="p-0 border-top-0">

                                    <div
                                        class="collapse"
                                        id="outgoingItems{{ $transfer->id }}"
                                    >
                                        <div class="p-3 bg-light">

                                            <h6 class="small mb-2">
                                                Order Products
                                            </h6>

                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered bg-white mb-0">

                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>
                                                                Quantity Sent by Admin
                                                            </th>
                                                            <th>
                                                                Quantity Received
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach($transfer->items as $item)
                                                            <tr>
                                                                <td>
                                                                    {{ $item->orderItem->product->name
                                                                        ?? 'Product not found' }}
                                                                </td>

                                                                <td>
                                                                    {{ number_format(
                                                                        (float) $item->quantity_sent,
                                                                        2
                                                                    ) }}
                                                                </td>

                                                                <td>
                                                                    {{ number_format(
                                                                        (float) ($item->quantity_received ?? 0),
                                                                        2
                                                                    ) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>

                                                </table>
                                            </div>

                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        class="btn btn-link btn-sm text-decoration-none"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#outgoingItems{{ $transfer->id }}"
                                        aria-expanded="false"
                                        aria-controls="outgoingItems{{ $transfer->id }}"
                                    >
                                        <i
                                            data-feather="package"
                                            style="width:13px;height:13px;"
                                        ></i>
                                        View Products
                                    </button>

                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">

                                    <i
                                        data-feather="inbox"
                                        style="width:32px;height:32px;"
                                    ></i>

                                    <p class="small text-muted mt-2 mb-0">
                                        Send করার মতো কোনো Received Order নেই।
                                    </p>

                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $transfers->links() }}
            </div>

        </div>
    </div>

</div>
@endsection