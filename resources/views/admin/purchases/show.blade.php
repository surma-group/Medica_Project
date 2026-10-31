@extends('layouts.admin')

@section('content')

<section class="section">

<div class="row">
    <div class="col-12">

```
    <div class="card shadow-sm">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center no-print">

            <h4>
                Purchase #{{ $purchase->purchase_number }}
            </h4>

            <div>

                <a href="{{ route('purchases.index') }}"
                   class="btn btn-secondary btn-sm">
                    Back
                </a>

                <button type="button"
                        onclick="window.print()"
                        class="btn btn-primary btn-sm">
                    Print
                </button>

            </div>

        </div>

        <div class="card-body">

            {{-- PRINT AREA --}}
            <div id="printArea">

                {{-- HEADER --}}
                <div class="text-center mb-4">
                    <h3>Medica Store</h3>
                    <p class="mb-1">Purchase Invoice</p>
                    <hr>
                </div>

                {{-- INFO --}}
                <div class="row mb-4">

                    {{-- SUPPLIER --}}
                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <h6 class="mb-3"><strong>Supplier Info</strong></h6>

                            <p class="mb-1">
                                <strong>Supplier :</strong>
                                {{ $purchase->supplier->code ?? '' }}
                                {{ $purchase->supplier->name ?? '-' }}
                            </p>

                            <p class="mb-1">
                                <strong>Purchase No:</strong>
                                {{ $purchase->purchase_number }}
                            </p>

                            <p class="mb-0">
                                <strong>Status:</strong>
                                <span class="badge bg-success">Active</span>
                            </p>

                        </div>

                    </div>

                    {{-- PURCHASE --}}
                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100 text-md-end">

                            <h6 class="mb-3"><strong>Purchase Info</strong></h6>

                            <p class="mb-1">
                                <strong>Date:</strong>
                                {{ $purchase->created_at->format('d M Y h:i A') }}
                            </p>

                            <p class="mb-1">
                                <strong>Payment:</strong>

                                @if($purchase->payment_method == 1)
                                    Cash
                                @else
                                    Online
                                @endif

                            </p>

                            <p class="mb-0">
                                <strong>Total Items:</strong>
                                {{ $purchase->items->count() }}
                            </p>

                        </div>

                    </div>

                </div>

                {{-- TABLE --}}
                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($purchase->items as $item)

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $item->product->name ?? 'Deleted Product' }}</strong></td>
                                <td>{{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit->title ?? '-' }}</td>
                                <td>{{ number_format($item->total, 2) }}</td>
                            </tr>

                            @empty

                            <tr>
                                <td colspan="6" class="text-center">No Items Found</td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- TOTAL + NOTE --}}
                <div class="row mt-4">

                    <div class="col-md-6">

                        @if($purchase->note)
                        <div class="note-line">
                            <strong>Note:</strong>
                            <span>{{ $purchase->note }}</span>
                        </div>
                        @endif

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3">

                            <div class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span>{{ number_format($purchase->subtotal, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span>Delivery Charge</span>
                                <span>{{ number_format($purchase->delivery_charge, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span>Discount</span>
                                <span>-{{ number_format($purchase->discount, 2) }}</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <h5><strong>Grand Total</strong></h5>
                                <h5><strong>{{ number_format($purchase->grand_total, 2) }}</strong></h5>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="text-center mt-5">
                    <p class="mb-0">Thank you for your purchase.</p>
                </div>

            </div>

        </div>

    </div>

</div>
```

</div>

</section>

@endsection

@push('styles')

<style>

/* =========================
   NOTE STYLE (IMPORTANT)
========================= */
.note-line{
    margin-top: 10px;
    font-size: 12px;
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

/* =========================
   PRINT STYLE
========================= */
@media print {

    @page {
        size: A4;
        margin: 12mm;
    }

    body {
        margin: 0 !important;
        padding: 0 !important;
        font-size: 12px !important;
        color: #000 !important;
        background: #fff !important;
    }

    body * {
        visibility: hidden !important;
    }

    #printArea,
    #printArea * {
        visibility: visible !important;
    }

    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    /* TABLE FIX */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    th, td {
        border: 1px solid #e0e0e0 !important;
        padding: 5px !important;
    }

    thead {
        background: #f7f7f7 !important;
    }

    /* ROW FIX */
    .row {
        display: flex !important;
        width: 100% !important;
    }

    .col-md-6 {
        width: 50% !important;
        flex: 0 0 50% !important;
    }

    /* UI HIDE */
    .no-print,
    .btn,
    nav,
    aside,
    header,
    footer,
    .navbar,
    .sidebar {
        display: none !important;
    }

    /* NOTE PRINT FIX */
    .note-line{
        display: block !important;
        margin-top: 8px !important;
    }

}
</style>

@endpush
