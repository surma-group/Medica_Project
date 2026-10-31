@extends('layouts.partner')

@section('content')
<section class="section">

    <div class="row">
        <div class="col-12">

            <div class="card">

                {{-- HEADER (NOT PRINT) --}}
                <div class="card-header d-flex justify-content-between align-items-center no-print">

                    <h4>Order #{{ $order->order_number }}</h4>

                    <div>
                        <a href="{{ route('partner.orders') }}" class="btn btn-secondary btn-sm">
                            Back
                        </a>

                        <button type="button" onclick="window.print()" class="btn btn-primary btn-sm">
                            Print
                        </button>
                    </div>

                </div>

                <div class="card-body">

                    {{-- ================= PRINT AREA START ================= --}}
                    <div id="printArea">

                        {{-- COMPANY INFO --}}
                        <div class="text-center mb-4">

                            <h3>Medica</h3>
                            <p>
                                Address Line Here <br>
                                Phone: 01XXXXXXXXX
                            </p>

                            <hr>

                            <h5>INVOICE</h5>

                        </div>

                        {{-- ORDER + CUSTOMER INFO --}}
                        <div class="row mb-4">

                            <div class="col-md-6">

                                <h6>Customer Info</h6>

                                <p>
                                    <strong>Name:</strong>
                                    {{ $order->customer->name ?? 'N/A' }} <br>

                                    <strong>Phone:</strong>
                                    {{ $order->customer->phone ?? 'N/A' }}
                                </p>

                            </div>

                            <div class="col-md-6 text-md-end">

                                <h6>Order Info</h6>

                                <p>
                                    <strong>Order No:</strong> {{ $order->order_number }} <br>
                                    <strong>Date:</strong> {{ $order->created_at->format('d M Y H:i') }} <br>

                                    <strong>Status:</strong>

                                    {{ $order->status_label }}

                                </p>

                            </div>

                        </div>

                        {{-- ITEMS --}}
                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($order->items as $key => $item)

                                    <tr>

                                        <td>{{ $key + 1 }}</td>

                                        <td>
                                            {{ $item->product->name ?? 'N/A' }}
                                        </td>

                                        <td>{{ number_format($item->price, 2) }}</td>

                                        <td>{{ $item->quantity }}</td>

                                        <td>{{ number_format($item->total, 2) }}</td>

                                    </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                        {{-- TOTALS --}}
                        <div class="row mt-4">

                            <div class="col-md-6">

                                @if($order->note)
                                <strong>Note:</strong>
                                <p>{{ $order->note }}</p>
                                @endif

                            </div>

                            <div class="col-md-6 text-end">

                                <table class="table table-sm">

                                    <tr>
                                        <th>Subtotal:</th>
                                        <td>{{ number_format($order->subtotal, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <th>Delivery:</th>
                                        <td>{{ number_format($order->delivery_charge, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <th>Discount:</th>
                                        <td>{{ number_format($order->discount, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <th>Grand Total:</th>
                                        <td>
                                            <strong>{{ number_format($order->grand_total, 2) }}</strong>
                                        </td>
                                    </tr>

                                </table>

                            </div>

                        </div>

                        {{-- FOOTER --}}
                        <div class="text-center mt-5">

                            <p>Thank you for your business</p>

                        </div>

                    </div>
                    {{-- ================= PRINT AREA END ================= --}}

                </div>

            </div>

        </div>
    </div>

</section>
@endsection

{{-- STYLE --}}
@push('styles')
<style>
    @media print {

        /* hide everything */
        body * {
            visibility: hidden !important;
        }

        /* show only invoice */
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

        /* hide UI elements */
        .no-print,
        .btn,
        nav,
        aside,
        header,
        footer {
            display: none !important;
        }

        body {
            background: #fff !important;
        }
    }
</style>
@endpush