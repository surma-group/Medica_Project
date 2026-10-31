@extends('layouts.admin')

@section('content')
<section class="section">

    <div class="row">
        <div class="col-12">

            <div class="card shadow-sm">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center no-print">

                    <h4>
                        Order #{{ $order->order_number }}
                    </h4>

                    <div>

                        <a href="{{ route('orders.index') }}"
                            class="btn btn-secondary btn-sm">
                            Back
                        </a>

                        @if($order->status == ORDER_STATUS_PENDING)

                        <form action="{{ route('orders.update-status', $order->id) }}"
                            method="POST"
                            class="d-inline">
                            @csrf

                            <input type="hidden"
                                name="status"
                                value="{{ ORDER_STATUS_ACCEPTED }}">

                            <button type="submit"
                                class="btn btn-success btn-sm"
                                onclick="return confirm('Accept this order?')">
                                Accept
                            </button>
                        </form>

                        @endif

                        <button type="button"
                            onclick="window.print()"
                            class="btn btn-primary btn-sm">
                            Print
                        </button>

                    </div>

                </div>

                <div class="card-body">

                    {{-- ================= PRINT AREA START ================= --}}
                    <div id="printArea">

                        {{-- COMPANY HEADER --}}
                        <div class="text-center mb-4">

                            <h3>Medica</h3>

                            <p class="mb-1">
                                Order Invoice
                            </p>

                            <hr>

                        </div>

                        {{-- CUSTOMER / DEALER / AGENT INFO --}}
                        <div class="row mb-4">

                            <div class="col-md-6">

                                <div class="border rounded p-3 h-100">

                                    <h6 class="mb-3">
                                        <strong>User Information</strong>
                                    </h6>

                                    @if($order->user_type_id == USER_TYPE_CUSTOMER)

                                    <p class="mb-1">
                                        <strong>Type:</strong>
                                        Customer
                                    </p>

                                    <p class="mb-1">
                                        <strong>Name:</strong>
                                        {{ $order->customer->name ?? '-' }}
                                    </p>

                                    <p class="mb-1">
                                        <strong>Phone:</strong>
                                        {{ $order->customer->phone ?? '-' }}
                                    </p>

                                    <p class="mb-1">
                                        <strong>Email:</strong>
                                        {{ $order->customer->email ?? '-' }}
                                    </p>

                                    <p class="mb-0">
                                        <strong>Address:</strong>
                                        {{ $order->customer->address ?? '-' }}
                                    </p>

                                    @else

                                    <p class="mb-1">
                                        <strong>Type:</strong>

                                        @if($order->user_type_id == USER_TYPE_DEALER)
                                        Dealer
                                        @elseif($order->user_type_id == USER_TYPE_AGENT)
                                        Agent
                                        @else
                                        Partner
                                        @endif
                                    </p>

                                    <p class="mb-1">
                                        <strong>Name:</strong>
                                        {{ $order->partner->shop_name ?? $order->partner->name ?? '-' }}
                                    </p>

                                    <p class="mb-1">
                                        <strong>Phone:</strong>
                                        {{ $order->partner->phone ?? '-' }}
                                    </p>

                                    <p class="mb-1">
                                        <strong>Email:</strong>
                                        {{ $order->partner->email ?? '-' }}
                                    </p>

                                    <p class="mb-0">
                                        <strong>Address:</strong>
                                        {{ $order->partner->address ?? '-' }}
                                    </p>

                                    @endif

                                </div>

                            </div>

                            {{-- ORDER INFO --}}
                            <div class="col-md-6">

                                <div class="border rounded p-3 h-100 text-md-end">

                                    <h6 class="mb-3">
                                        <strong>Order Information</strong>
                                    </h6>

                                    <p class="mb-1">
                                        <strong>Order No:</strong>
                                        {{ $order->order_number }}
                                    </p>

                                    <p class="mb-1">
                                        <strong>Date:</strong>
                                        {{ $order->created_at->format('d M Y h:i A') }}
                                    </p>

                                    <p class="mb-1">
                                        <strong>Payment:</strong>

                                        @if($order->payment_method == 1)
                                        Cash
                                        @else
                                        Online
                                        @endif
                                    </p>

                                    <p class="mb-0">
                                        <strong>Status:</strong>

                                        <span class="badge bg-primary">
                                            {{ $order->status_label }}
                                        </span>
                                    </p>
                                </div>

                            </div>

                        </div>

                        {{-- PRODUCT TABLE --}}
                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead class="table-light">

                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Image</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @forelse($order->items as $item)

                                    <tr>

                                        <td>
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>
                                            <strong>
                                                {{ $item->product->name ?? 'Deleted Product' }}
                                            </strong>
                                        </td>

                                        <td>

                                            @if($item->product && $item->product->image)

                                            <img src="{{ asset('public/storage/' . $item->product->image) }}"
                                                width="50"
                                                height="50"
                                                style="object-fit:cover;border-radius:6px;">

                                            @else

                                            No Image

                                            @endif

                                        </td>

                                        <td>
                                            {{ number_format($item->price, 2) }}
                                        </td>

                                        <td>
                                            {{ $item->quantity }}
                                        </td>

                                        <td>
                                            {{ number_format($item->total, 2) }}
                                        </td>

                                    </tr>

                                    @empty

                                    <tr>
                                        <td colspan="6" class="text-center">
                                            No Products Found
                                        </td>
                                    </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                        {{-- TOTALS --}}
                        <div class="row mt-4">

                            <div class="col-md-6">

                                @if($order->note)

                                <div class="border rounded p-3">

                                    <strong>Note:</strong>

                                    <p class="mb-0 mt-2">
                                        {{ $order->note }}
                                    </p>

                                </div>

                                @endif

                            </div>

                            <div class="col-md-6">

                                <div class="border rounded p-3">

                                    <div class="d-flex justify-content-between">
                                        <span>Subtotal</span>
                                        <span>{{ number_format($order->subtotal, 2) }}</span>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <span>Delivery Charge</span>
                                        <span>{{ number_format($order->delivery_charge, 2) }}</span>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <span>Discount</span>
                                        <span>-{{ number_format($order->discount, 2) }}</span>
                                    </div>

                                    <hr>

                                    <div class="d-flex justify-content-between">

                                        <h5>
                                            <strong>Grand Total</strong>
                                        </h5>

                                        <h5>
                                            <strong>
                                                {{ number_format($order->grand_total, 2) }}
                                            </strong>
                                        </h5>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- FOOTER --}}
                        <div class="text-center mt-5">

                            <p class="mb-0">
                                Thank you for your business.
                            </p>

                        </div>

                    </div>
                    {{-- ================= PRINT AREA END ================= --}}

                </div>

            </div>

        </div>
    </div>

</section>
@endsection

@push('styles')
<style>
    @media print {

        /* =========================
       GLOBAL HIDE EVERYTHING
    ========================== */
        body * {
            visibility: hidden !important;
        }

        /* =========================
       PRINT AREA ONLY
    ========================== */
        #printArea,
        #printArea * {
            visibility: visible !important;
        }

        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            font-size: 12px !important;
            color: #000 !important;
        }

        /* =========================
       BOOTSTRAP GRID FIX (IMPORTANT)
    ========================== */
        .row {
            display: flex !important;
            flex-wrap: nowrap !important;
            margin: 0 !important;
        }

        .col-md-6 {
            width: 50% !important;
            flex: 0 0 50% !important;
            max-width: 50% !important;
        }

        /* =========================
       TYPOGRAPHY COMPACT
    ========================== */
        #printArea h1,
        #printArea h2,
        #printArea h3,
        #printArea h4,
        #printArea h5,
        #printArea h6 {
            margin: 2px 0 !important;
            padding: 0 !important;
            line-height: 1.2 !important;
        }

        #printArea p {
            margin: 2px 0 !important;
            line-height: 1.2 !important;
        }

        /* =========================
       TABLE FIX
    ========================== */
        table {
            font-size: 12px !important;
            width: 100% !important;
            border-collapse: collapse !important;
        }

        table th,
        table td {
            padding: 4px 6px !important;
            border: 1px solid #000 !important;
        }

        /* =========================
       REMOVE UI ELEMENTS
    ========================== */
        .no-print,
        .btn,
        nav,
        aside,
        header,
        footer,
        .main-sidebar,
        .navbar,
        .main-footer {
            display: none !important;
        }

        /* =========================
       PAGE SETUP
    ========================== */
        body {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        /* =========================
       AVOID SPLIT ISSUES
    ========================== */
        tr,
        td,
        th,
        table,
        .border {
            page-break-inside: avoid !important;
        }

        /* =========================
       IMAGE FIX
    ========================== */
        img {
            max-width: 60px !important;
            height: auto !important;
        }
    }
</style>
@endpush