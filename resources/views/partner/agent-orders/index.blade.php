
@extends('layouts.partner')

@section('content')

@php
    $partner = \App\Models\Partner::where('user_id', Auth::id())->first();

    abort_unless(
        (int) ($partner->type ?? 0) === USER_TYPE_DEALER,
        403
    );
@endphp

<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Agent Orders</h4>
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

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>

                {{-- FILTERS --}}
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('partner.agent-orders') }}">
                        <div class="row align-items-end">

                            {{-- AGENT FILTER --}}
                            <div class="col-md-3 mb-3">
                                <label for="agent_id" class="form-label">Agent</label>
                                <select name="agent_id" id="agent_id" class="form-control">
                                    <option value="">All Agents</option>

                                    @foreach($agents as $agent)
                                        <option
                                            value="{{ $agent->id }}"
                                            {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name ?? ('Agent #' . $agent->id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- STATUS FILTER --}}
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Statuses</option>

                                    <option value="{{ ORDER_STATUS_PENDING }}"
                                        {{ request('status') == ORDER_STATUS_PENDING && request('status') !== null ? 'selected' : '' }}>
                                        Pending
                                    </option>

                                    <option value="{{ ORDER_STATUS_ACCEPTED }}"
                                        {{ request('status') == ORDER_STATUS_ACCEPTED && request('status') !== null ? 'selected' : '' }}>
                                        Accepted
                                    </option>

                                    <option value="{{ ORDER_STATUS_ON_THE_WAY }}"
                                        {{ request('status') == ORDER_STATUS_ON_THE_WAY && request('status') !== null ? 'selected' : '' }}>
                                        On The Way
                                    </option>

                                    <option value="{{ ORDER_STATUS_COMPLETED }}"
                                        {{ request('status') == ORDER_STATUS_COMPLETED && request('status') !== null ? 'selected' : '' }}>
                                        Completed
                                    </option>

                                    <option value="{{ ORDER_STATUS_REJECTED }}"
                                        {{ request('status') == ORDER_STATUS_REJECTED && request('status') !== null ? 'selected' : '' }}>
                                        Cancelled
                                    </option>
                                </select>
                            </div>

                            {{-- FROM DATE --}}
                            <div class="col-md-2 mb-3">
                                <label for="from_date" class="form-label">From Date</label>
                                <input
                                    type="date"
                                    name="from_date"
                                    id="from_date"
                                    class="form-control"
                                    value="{{ request('from_date') }}">
                            </div>

                            {{-- TO DATE --}}
                            <div class="col-md-2 mb-3">
                                <label for="to_date" class="form-label">To Date</label>
                                <input
                                    type="date"
                                    name="to_date"
                                    id="to_date"
                                    class="form-control"
                                    value="{{ request('to_date') }}">
                            </div>

                            {{-- BUTTONS --}}
                            <div class="col-md-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter"></i> Filter
                                </button>

                                <a href="{{ route('partner.agent-orders') }}"
                                   class="btn btn-secondary btn-sm">
                                    Reset
                                </a>
                            </div>

                        </div>
                    </form>
                </div>

                {{-- ORDERS TABLE --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md mb-0">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order No</th>
                                    <th>Agent</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            {{ $orders->firstItem() + $loop->index }}
                                        </td>

                                        <td>
                                            {{ $order->order_number }}
                                        </td>

                                        <td>
                                            {{ $order->partner->name ?? 'N/A' }}
                                            <br>
                                            <small>
                                                {{ $order->partner->phone ?? '' }}
                                            </small>
                                        </td>

                                        <td>
                                            {{ number_format($order->grand_total, 2) }}
                                        </td>

                                        <td>
                                            @php
                                                $statusClass = match ((int) $order->status) {
                                                    ORDER_STATUS_PENDING => 'bg-warning text-dark',
                                                    ORDER_STATUS_ACCEPTED => 'bg-info text-dark',
                                                    ORDER_STATUS_ON_THE_WAY => 'bg-primary',
                                                    ORDER_STATUS_COMPLETED => 'bg-success',
                                                    ORDER_STATUS_REJECTED => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp

                                            <span class="badge {{ $statusClass }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $order->created_at?->format('d M Y') ?? 'N/A' }}
                                        </td>

                                        <td class="text-end">
                                            <a
                                                href="{{ route('partner.agent-orders.show', $order->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            No agent orders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>

                {{-- PAGINATION --}}
                @if($orders->hasPages())
                    <div class="card-footer">
                        {{ $orders->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</section>

@endsection
