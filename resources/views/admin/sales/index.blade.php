@extends('layouts.admin')

@section('content')

<section class="section">
    <div class="row">
        <div class="col-12">

        <div class="card">

            {{-- HEADER --}}
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sales</h5>

                <a href="{{ route('sales.create') }}" class="btn btn-success btn-sm">
                    New Sale
                </a>
            </div>

            {{-- COMPACT SEARCH --}}
            <div class="card-body py-2 border-bottom">

                <form action="{{ route('sales.index') }}" method="GET">

                    <div class="row g-2 align-items-center">

                        {{-- DATE FROM --}}
                        <div class="col-md-3">
                            <input type="date"
                                name="date_from"
                                class="form-control form-control-sm"
                                value="{{ request('date_from', now()->format('Y-m-d')) }}"
                                title="Date From">
                        </div>

                        {{-- DATE TO --}}
                        <div class="col-md-3">
                            <input type="date"
                                name="date_to"
                                class="form-control form-control-sm"
                                value="{{ request('date_to', now()->format('Y-m-d')) }}"
                                title="Date To">
                        </div>

                        {{-- SALE NUMBER --}}
                        <div class="col-md-4">
                            <input type="text"
                                name="sale_number"
                                class="form-control form-control-sm"
                                placeholder="Search Sale No"
                                value="{{ request('sale_number') }}">
                        </div>

                        {{-- BUTTONS --}}
                        <div class="col-md-2 d-flex gap-1">

                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-search"></i>
                                Search
                            </button>

                            <a href="{{ route('sales.index') }}"
                                class="btn btn-secondary btn-sm">
                                Reset
                            </a>

                        </div>

                    </div>

                </form>

            </div>

            {{-- TABLE --}}
            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-striped table-sm mb-0">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sale No</th>
                                <th>Created By</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($sales as $sale)

                            <tr>

                                <td>
                                    {{ $sales->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    {{ $sale->sale_number }}
                                </td>

                                <td>
                                    {{ $sale->creator->name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $sale->customer->code ?? '' }}
                                    {{ $sale->customer->name ?? '-' }}
                                </td>

                                <td>
                                    {{ number_format($sale->grand_total, 2) }}
                                </td>

                                <td>

                                    @if($sale->status == 0)

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    @elseif($sale->status == 1)

                                        <span class="badge bg-success text-white">
                                            Completed
                                        </span>

                                    @elseif($sale->status == 2)

                                        <span class="badge bg-primary text-white">
                                            Processing
                                        </span>

                                    @elseif($sale->status == 3)

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
                                    {{ $sale->created_at->format('d M, Y') }}
                                </td>

                                <td>

                                    <a href="{{ route('sales.show', $sale->id) }}"
                                        class="btn btn-sm btn-info py-0 px-2">
                                        View
                                    </a>

                                    <form action="{{ route('sales.destroy', $sale->id) }}"
                                        method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Are you sure?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="btn btn-sm btn-danger py-0 px-2">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="8" class="text-center py-2">
                                    No sales found.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            {{-- PAGINATION --}}
            <div class="card-footer py-2 text-end">
                {{ $sales->links() }}
            </div>

        </div>

    </div>
</div>

</section>

@endsection
