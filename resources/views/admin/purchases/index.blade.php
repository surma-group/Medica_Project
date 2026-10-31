@extends('layouts.admin')

@section('content')

<section class="section">
    <div class="row">
        <div class="col-12">
        <div class="card">

            {{-- HEADER --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Purchases</h4>

                <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                    Add Purchase
                </a>
            </div>

            {{-- SEARCH / FILTER --}}
            <div class="card-body border-bottom">

                <form action="{{ route('purchases.index') }}" method="GET">

                    <div class="row g-3">

                        {{-- DATE FROM --}}
                        <div class="col-md-3">
                            <label class="form-label">Date From</label>

                            <input type="date"
                                name="date_from"
                                class="form-control"
                                value="{{ request('date_from', now()->format('Y-m-d')) }}">
                        </div>

                        {{-- DATE TO --}}
                        <div class="col-md-3">
                            <label class="form-label">Date To</label>

                            <input type="date"
                                name="date_to"
                                class="form-control"
                                value="{{ request('date_to', now()->format('Y-m-d')) }}">
                        </div>

                        {{-- SUPPLIER --}}
                        <div class="col-md-3">
                            <label class="form-label">Supplier</label>

                            <select name="supplier_id" class="form-control">
                                <option value="">All Suppliers</option>

                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->code ?? '' }}
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- PURCHASE NO --}}
                        <div class="col-md-3">
                            <label class="form-label">Purchase No</label>

                            <input type="text"
                                name="purchase_number"
                                class="form-control"
                                placeholder="Search Purchase No"
                                value="{{ request('purchase_number') }}">
                        </div>

                        {{-- BUTTONS --}}
                        <div class="col-12 d-flex justify-content-end gap-2">

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Search
                            </button>

                            <a href="{{ route('purchases.index') }}"
                                class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i>
                                Reset
                            </a>

                        </div>

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
                                <th>Purchase No</th>
                                <th>User Type</th>
                                <th>Reference</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th style="width:150px;">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($purchases as $purchase)

                            @php
                                $reference = $purchase->reference_id ?? '-';
                            @endphp

                            <tr>

                                <td>
                                    {{ $purchases->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    {{ $purchase->purchase_number }}
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $purchase->userType->name ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $purchase->supplier->code ?? '' }}
                                    {{ $purchase->supplier->name ?? '-' }}
                                </td>

                                <td>
                                    {{ number_format($purchase->grand_total, 2) }}
                                </td>

                                <td>

                                    @if($purchase->status == 0)

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    @elseif($purchase->status == 1)

                                        <span class="badge bg-success text-white">
                                            Completed
                                        </span>

                                    @elseif($purchase->status == 2)

                                        <span class="badge bg-primary text-white">
                                            Processing
                                        </span>

                                    @elseif($purchase->status == 3)

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
                                    {{ $purchase->created_at->format('d M, Y') }}
                                </td>

                                <td>

                                    <a href="{{ route('purchases.show', $purchase->id) }}"
                                        class="btn btn-sm btn-info">
                                        View
                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('purchases.destroy', $purchase->id) }}"
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
                                    No purchases found.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            {{-- PAGINATION --}}
            <div class="card-footer text-end">
                {{ $purchases->links() }}
            </div>

        </div>

    </div>
</div>

</section>
@endsection
