@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">

            <div class="card">

                {{-- HEADER --}}
                <div class="card-header">
                    <h4>Product Stocks</h4>
                </div>

                {{-- FILTER --}}
                <div class="card-body py-2 border-bottom">

                    <form action="{{ route('product-stock.index') }}" method="GET">

                        <div class="row g-2 align-items-center">

                            {{-- PRODUCT NAME --}}
                            <div class="col-md-5">

                                <input
                                    type="text"
                                    name="product_name"
                                    class="form-control form-control-sm"
                                    placeholder="Search Product Name"
                                    value="{{ request('product_name') }}">

                            </div>

                            {{-- STOCK STATUS --}}
                            <div class="col-md-3">

                                <select
                                    name="stock_status"
                                    class="form-control form-control-sm">

                                    <option value="">
                                        All Stock
                                    </option>

                                    <option
                                        value="in_stock"
                                        {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>
                                        In Stock
                                    </option>

                                    <option
                                        value="out_of_stock"
                                        {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>
                                        Out of Stock
                                    </option>

                                </select>

                            </div>

                            {{-- BUTTONS --}}
                            <div class="col-md-4 d-flex gap-1">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm">

                                    <i class="fas fa-search"></i>
                                    Search

                                </button>

                                <a
                                    href="{{ route('product-stock.index') }}"
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

                        <table class="table table-striped table-md">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Strength</th>
                                    <th>Company</th>
                                    <th>Current Stock</th>
                                    <th>Cost Price</th>
                                    <th>Last Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($stocks as $stock)

                                @php
                                $product = $stock->product;
                                $baseQty = $stock->quantity;

                                $box = 0;
                                $piece = 0;

                                if ($product && $product->conversion_rate > 0 && $product->secondary_unit_id) {
                                $box = intdiv($baseQty, $product->conversion_rate);
                                $piece = $baseQty % $product->conversion_rate;
                                } else {
                                $piece = $baseQty;
                                }
                                @endphp

                                <tr>

                                    <td>
                                        {{ $stocks->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        {{ $product->name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $product->strength ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $product->company->company_name ?? 'N/A' }}
                                    </td>

                                    {{-- STOCK DISPLAY --}}
                                    <td>
                                        @if($stock->quantity > 0)
                                        <span class="badge bg-success">
                                            {{ $stock->formatted_stock['text'] }}
                                        </span>
                                        @else
                                        <span class="badge bg-danger">
                                            Out of Stock
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $stock->formatted_stock['text'] }}</div>

                                        <small>
                                            @foreach($stock->unit_costs ?? [] as $unit => $price)
                                            {{ $unit }}: {{ number_format($price, 2) }} ৳ <br>
                                            @endforeach
                                        </small>
                                    </td>

                                    <td>
                                        {{ $stock->updated_at?->format('d M, Y h:i A') }}
                                    </td>

                                    <td>
                                        <button
                                            class="btn btn-sm btn-info"
                                            onclick="loadStockHistory({{ $stock->product_id }})"
                                            data-bs-toggle="modal"
                                            data-bs-target="#stockModal">
                                            Details
                                        </button>
                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="6" class="text-center">
                                        No stock records found.
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                {{-- PAGINATION --}}
                <div class="card-footer text-end">
                    {{ $stocks->links() }}
                </div>

            </div>

        </div>
    </div>
</section>

{{-- STOCK HISTORY MODAL --}}
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Stock History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div id="stockHistoryContent">
                    <p class="text-center">Loading...</p>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    function loadStockHistory(productId) {
        document.getElementById('stockHistoryContent').innerHTML =
            '<p class="text-center">Loading...</p>';

        fetch(`/em_admin/product-stock/history/${productId}`)
            .then(res => res.json())
            .then(data => {

                let html = `
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Reference</th>
                            <th>Note</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                if (data.length === 0) {
                    html += `<tr><td colspan="5" class="text-center">No data found</td></tr>`;
                }

                data.forEach(log => {

                    let type = '';

                    if (log.type == 1) type = '<span class="badge bg-success">Purchase</span>';
                    else if (log.type == 2) type = '<span class="badge bg-danger">Sale</span>';
                    else if (log.type == 3) type = '<span class="badge bg-warning">Return</span>';
                    else type = '<span class="badge bg-secondary">Adjust</span>';

                    html += `
                    <tr>
                        <td>${type}</td>
                        <td>${log.formatted_quantity ?? log.quantity}</td>
                        <td>${log.reference_type ?? '-'} #${log.reference_id ?? '-'}</td>
                        <td>${log.note ?? '-'}</td>
                        <td>${log.created_at ?? ''}</td>
                    </tr>
                `;
                });

                html += `</tbody></table>`;

                document.getElementById('stockHistoryContent').innerHTML = html;
            })
            .catch(err => {
                document.getElementById('stockHistoryContent').innerHTML =
                    '<p class="text-danger text-center">Failed to load data</p>';
            });
    }
</script>
@endpush