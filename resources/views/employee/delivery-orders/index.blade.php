@extends('layouts.employee')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- Card Header --}}
                <div class="card-header d-flex justify-content-between align-items-center">

                    <h4>Delivery Orders</h4>

                    <div class="d-flex align-items-center gap-2">

                        <strong>
                            Total:
                            <span class="text-primary">
                                {{ $transfers->total() }}
                            </span>
                        </strong>

                        {{-- Order Filter --}}
                        <form action="{{ route('employee.delivery-orders.index') }}"
                              method="GET">

                            <select name="filter"
                                    class="form-control"
                                    onchange="this.form.submit()">

                                <option value="assigned"
                                    {{ $filter == 'assigned' ? 'selected' : '' }}>
                                    Assigned Orders
                                </option>

                                <option value="delivered"
                                    {{ $filter == 'delivered' ? 'selected' : '' }}>
                                    Delivered Orders
                                </option>

                                <option value="all"
                                    {{ $filter == 'all' ? 'selected' : '' }}>
                                    All Orders
                                </option>

                            </select>
                        </form>

                    </div>
                </div>

                {{-- Card Body --}}
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-striped table-md">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transfer Number</th>
                                    <th>Order Number</th>
                                    <th>Agent Name</th>
                                    <th>Order Amount</th>
                                    <th>Assigned Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($transfers as $transfer)

                                <tr>

                                    {{-- Serial --}}
                                    <td>
                                        {{ $transfers->firstItem() + $loop->index }}
                                    </td>

                                    {{-- Transfer Number --}}
                                    <td>
                                        {{ $transfer->transfer_number }}
                                    </td>

                                    {{-- Order Number --}}
                                    <td>
                                        {{ $transfer->order->order_number ?? 'N/A' }}
                                    </td>

                                    {{-- Agent Name --}}
                                    <td>
                                        {{ $transfer->agent->name ?? 'N/A' }}
                                    </td>

                                    {{-- Transfer Amount --}}
                                    <td>
                                        {{ number_format($transfer->transfer_amount, 2) }}
                                    </td>

                                    {{-- Assigned Date --}}
                                    <td>
                                        {{ $transfer->sent_at
                                            ? \Carbon\Carbon::parse($transfer->sent_at)->format('d M Y')
                                            : $transfer->created_at->format('d M Y') }}
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @if($transfer->status == 2)
                                            <span class="badge badge-warning">
                                                Assigned
                                            </span>
                                        @elseif($transfer->status == 3)
                                            <span class="badge badge-success">
                                                Delivered
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Action --}}
                                    <td>
                                        <a href="{{ route('employee.delivery-orders.show', $transfer->id) }}"
                                           class="btn btn-primary btn-sm">

                                            <i class="fas fa-eye"></i>
                                            View

                                        </a>
                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        No delivery orders found.
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($transfers->hasPages())
                        <div class="card-footer d-flex justify-content-end">
                            {{ $transfers->links() }}
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</section>
@endsection