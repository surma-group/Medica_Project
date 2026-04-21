@extends('layouts.website')

@section('title', 'Cart')

@section('content')

<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Cart</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
        <li class="breadcrumb-item active text-white">Cart</li>
    </ol>
</div>

<div class="container-fluid py-5">
    <div class="container py-5">

        @php
        $cart = session('cart', []);
        $total = 0;
        @endphp

        @if(count($cart) > 0)
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                    @foreach($cart as $id => $item)
                    @php
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                    @endphp
                    <tr id="row-{{ $id }}">
                        <td>
                            <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : asset('website/img/product.jpg') }}"
                                style="width:70px;height:70px;object-fit:cover"
                                class="rounded-circle">
                        </td>

                        <td>{{ $item['name'] }}</td>

                        <td>${{ number_format($item['price'], 2) }}</td>

                        <td>
                            <div class="input-group cart-qty-container" style="width:110px;">
                                <button type="button"
                                    class="btn btn-sm btn-light border update-cart-btn"
                                    data-id="{{ $id }}"
                                    data-type="decrease">
                                    <i class="fa fa-minus"></i>
                                </button>

                                <input type="text"
                                    class="form-control form-control-sm text-center border-0 qty-input"
                                    value="{{ $item['quantity'] }}"
                                    readonly>

                                <button type="button"
                                    class="btn btn-sm btn-light border update-cart-btn"
                                    data-id="{{ $id }}"
                                    data-type="increase">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </td>

                        <td>${{ number_format($itemTotal, 2) }}</td>

                        <td>
                            <button type="button"
                                class="btn btn-sm btn-danger rounded-circle update-cart-btn"
                                data-id="{{ $id }}"
                                data-type="remove">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mt-5">
            <div class="col-md-5">

                <div class="bg-light rounded p-4">

                    <h3>Cart Total</h3>

                    @php
                    $delivery = 0;
                    $discount = 0;
                    $grandTotal = $total + $delivery - $discount;
                    @endphp

                    <!-- SUBTOTAL -->
                    <div class="d-flex justify-content-between mb-2">
                        <h5>Subtotal:</h5>
                        <h5>${{ number_format($total, 2) }}</h5>
                    </div>

                    <!-- DELIVERY -->
                    <div class="d-flex justify-content-between mb-2">
                        <h5>Delivery Charge:</h5>
                        <h5>${{ number_format($delivery, 2) }}</h5>
                    </div>

                    <!-- DISCOUNT -->
                    <div class="d-flex justify-content-between mb-2">
                        <h5>Discount:</h5>
                        <h5>-${{ number_format($discount, 2) }}</h5>
                    </div>

                    <hr>

                    <!-- GRAND TOTAL -->
                    <div class="d-flex justify-content-between">
                        <h5><strong>Total:</strong></h5>
                        <h5><strong>${{ number_format($grandTotal, 2) }}</strong></h5>
                    </div>

                    <!-- CHECKOUT -->
                    <a href="{{ route('checkout') }}" class="btn btn-primary w-100 mt-3">
                        Proceed Checkout
                    </a>

                </div>

            </div>
        </div>

        @else
        <div class="text-center py-5">
            <h3>Your cart is empty</h3>
            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">Go Shopping</a>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // রিকোয়েস্ট লক করার ভেরিয়েবল
        let isProcessing = false;

        // .off() ব্যবহার করে নিশ্চিত করা হচ্ছে যে আগের কোনো ইভেন্ট লেগে নেই
        $(document).off('click', '.update-cart-btn').on('click', '.update-cart-btn', function(e) {

            // সব ধরণের ডিফল্ট বিহেভিয়ার বন্ধ করা
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            let btn = $(this);

            // যদি অলরেডি প্রসেসিং হয় তবে কাজ করবে না
            if (isProcessing) return false;

            let id = btn.data('id');
            let type = btn.data('type');

            $.ajax({
                url: "{{ url('cart/update') }}/" + id + "/" + type,
                type: "GET",
                dataType: "json",
                beforeSend: function() {
                    isProcessing = true;
                    // বাটনগুলো ডিজেবল করে দেয়া যাতে ইউজার বারবার ক্লিক না করে
                    $('.update-cart-btn').prop('disabled', true).css('opacity', '0.6');
                },
                success: function(res) {
                    if (res.success) {
                        // পেজ রিফ্রেশ করে লেটেস্ট ডাটা দেখানো
                        window.location.reload();
                    } else {
                        alert('Something went wrong!');
                        isProcessing = false;
                        $('.update-cart-btn').prop('disabled', false).css('opacity', '1');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Server Error!');
                    isProcessing = false;
                    $('.update-cart-btn').prop('disabled', false).css('opacity', '1');
                }
            });
        });
    });
</script>
@endpush