@extends('layouts.partner')

@section('title', 'Incoming Transfers')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Incoming Transfers</h4>
            <p class="text-muted mb-0">
                Admin থেকে আপনার কাছে পাঠানো পণ্যের তালিকা
            </p>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Incoming Transfers Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Transfer List</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Transfer No.</th>
                            <th>Order No.</th>
                            <th>Products</th>
                            <th>Total Quantity</th>
                            <th>Transit Officer</th>
                            <th>Sent Date</th>
                            <th>Status</th>
                            <th width="100">Details</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transfers as $transfer)
                        @php
                        $totalQuantity = $transfer->items->sum(
                        fn ($item) => (float) $item->quantity_sent
                        );

                        $statusLabels = [
                        0 => ['Pending', 'bg-warning text-dark'],
                        1 => ['Assigned', 'bg-info text-dark'],
                        2 => ['Sent', 'bg-primary'],
                        3 => ['Received', 'bg-success'],
                        4 => ['Cancelled', 'bg-danger'],
                        ];

                        [$statusLabel, $statusClass] =
                        $statusLabels[$transfer->status]
                        ?? ['Unknown', 'bg-secondary'];
                        @endphp

                        <tr>
                            <td>
                                {{ $transfers->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <strong>
                                    {{ $transfer->transfer_number }}
                                </strong>
                            </td>

                            <td>
                                {{ $transfer->order->order_number
                                        ?? $transfer->order_id }}
                            </td>

                            <td>
                                {{ $transfer->items->count() }}
                            </td>

                            <td>
                                {{ number_format($totalQuantity, 3) }}
                            </td>

                            <td>
                                {{ $transfer->assignedEmployee->full_name
                                        ?? 'Not Assigned' }}
                            </td>

                            <td>
                                {{ $transfer->sent_at
                                        ? $transfer->sent_at->format('d M Y, h:i A')
                                        : '—' }}
                            </td>

                            <td>
                                <span class="badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <td>

                                @if((int) $transfer->status === 2)
                                <form
                                    action="{{ route('partner.incoming-transfers.receive', $transfer->id) }}"
                                    method="POST"
                                    class="mt-3"
                                    onsubmit="return confirm('আপনি কি এই Transfer-এর সব পণ্য Receive করতে চান?');">
                                    @csrf

                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i data-feather="check-circle" style="width: 14px; height: 14px;"></i>
                                        Receive
                                    </button>
                                </form>
                                @elseif((int) $transfer->status === 3)
                                <div class="alert alert-success mt-2 mb-0 py-1 px-2"
                                    style="font-size: 12px;">
                                    <i data-feather="check-circle" style="width: 12px; height: 12px;"></i>
                                    Transfer received.
                                </div>
                                @endif

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#transferItems{{ $transfer->id }}"
                                    aria-expanded="false">
                                    <i data-feather="eye"></i>
                                    View
                                </button>
                            </td>
                        </tr>

                        {{-- Product Details --}}
                        <tr class="collapse" id="transferItems{{ $transfer->id }}">
                            <td colspan="9" class="bg-light">
                                <div class="p-3">
                                    <h6 class="mb-3">
                                        Products in
                                        {{ $transfer->transfer_number }}
                                    </h6>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered bg-white mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th>Quantity Sent</th>
                                                    <th>Quantity Received</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse($transfer->items as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    <td>
                                                        {{ $item->orderItem->product->name
                                                                    ?? 'Product not found' }}
                                                    </td>

                                                    <td>
                                                        {{ number_format(
                                                                    (float) $item->quantity_sent,
                                                                    3
                                                                ) }}
                                                    </td>

                                                    <td>
                                                        @if($transfer->status === 3)
                                                        {{ number_format(
                                                                        (float) ($item->quantity_received ?? 0),
                                                                        3
                                                                    ) }}
                                                        @else
                                                        —
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        No products found.
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    @if($transfer->note)
                                    <div class="mt-3">
                                        <strong>Note:</strong>
                                        {{ $transfer->note }}
                                    </div>
                                    @endif

                                    @if($transfer->status === 2)
                                    <div class="alert alert-info mt-3 mb-0">
                                        <i data-feather="info"></i>
                                        পণ্যগুলো হাতে পাওয়ার পর সব পণ্য একসঙ্গে Receive করতে পারবেন।
                                    </div>
                                    @elseif($transfer->status === 3)
                                    <div class="alert alert-success mt-3 mb-0">
                                        এই transfer-এর সব পণ্য Receive করা হয়েছে।
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i data-feather="inbox" style="width: 40px; height: 40px;"></i>
                                <p class="mt-2 mb-0">
                                    কোনো Incoming Transfer পাওয়া যায়নি।
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