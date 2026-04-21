@extends('layouts.website')

@section('title', 'Checkout')

@section('content')

<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Checkout</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Home</a>
        </li>
        <li class="breadcrumb-item active text-white">Checkout</li>
    </ol>
</div>

@php
$cart = session('cart', []);
$subtotal = 0;
@endphp

<div class="container-fluid py-5">
    <div class="container py-5">

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <h1 class="mb-4">Billing details</h1>

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf

            <div class="row g-5">

                <!-- LEFT SIDE -->
                <div class="col-md-12 col-lg-6 col-xl-7">

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label my-3">First Name *</label>
                            <input type="text" name="first_name"
                                value="{{ old('first_name') }}"
                                class="form-control @error('first_name') is-invalid @enderror">
                            @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label my-3">Last Name *</label>
                            <input type="text" name="last_name"
                                value="{{ old('last_name') }}"
                                class="form-control @error('last_name') is-invalid @enderror">
                            @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <label class="form-label my-3">Address *</label>
                    <input type="text" name="address"
                        value="{{ old('address') }}"
                        class="form-control @error('address') is-invalid @enderror">
                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror

                    <!-- ✅ District Dropdown -->
                    <label class="form-label my-3">Town / City *</label>
                    <select name="city" class="form-control @error('city') is-invalid @enderror">
                        <option value="">Select District</option>
                        @foreach($districts as $district)
                        <option value="{{ $district->id }}"
                            {{ old('city') == $district->id ? 'selected' : '' }}>
                            {{ $district->name_en }}
                        </option>
                        @endforeach
                    </select>
                    @error('city') <small class="text-danger">{{ $message }}</small> @enderror

                    <label class="form-label my-3">Postcode *</label>
                    <input type="text" name="postcode"
                        value="{{ old('postcode') }}"
                        class="form-control">

                    <label class="form-label my-3">Mobile *</label>
                    <input type="text" name="phone"
                        value="{{ old('phone') }}"
                        class="form-control @error('phone') is-invalid @enderror">
                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror

                    <label class="form-label my-3">Email *</label>
                    <input type="email" name="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror

                    <label class="form-label my-3">Order Notes</label>
                    <textarea name="note" class="form-control" rows="5">{{ old('note') }}</textarea>

                </div>

                <!-- RIGHT SIDE -->
                <div class="col-md-12 col-lg-6 col-xl-5">

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($cart as $item)

                                @php
                                $itemTotal = $item['price'] * $item['quantity'];
                                $subtotal += $itemTotal;
                                @endphp

                                <tr>
                                    <td>
                                        <img src="{{ $item['image'] 
                                            ? asset('storage/'.$item['image']) 
                                            : asset('website/img/product.jpg') }}"
                                            style="width:70px;height:70px;border-radius:50%;">
                                    </td>

                                    <td>{{ $item['name'] }}</td>
                                    <td>${{ number_format($item['price'],2) }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td>${{ number_format($itemTotal,2) }}</td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-danger">
                                        Cart is empty!
                                    </td>
                                </tr>
                                @endforelse

                                @php
                                $delivery = 0;
                                $discount = 0;
                                $total = $subtotal + $delivery - $discount;
                                @endphp

                                <tr>
                                    <td colspan="3"></td>
                                    <td><strong>Subtotal</strong></td>
                                    <td>${{ number_format($subtotal,2) }}</td>
                                </tr>

                                <tr>
                                    <td colspan="3"></td>
                                    <td>Delivery</td>
                                    <td>${{ number_format($delivery,2) }}</td>
                                </tr>

                                <tr>
                                    <td colspan="3"></td>
                                    <td>Discount</td>
                                    <td>-${{ number_format($discount,2) }}</td>
                                </tr>

                                <tr>
                                    <td colspan="3"></td>
                                    <td><strong>Total</strong></td>
                                    <td><strong>${{ number_format($total,2) }}</strong></td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="form-check">
                            <input checked type="radio" name="payment" value="1" class="form-check-input">
                            <label>Cash On Delivery</label>
                        </div>
                    </div>

                    <button type="submit"
                        class="btn btn-primary w-100 mt-4 py-3"
                        {{ empty($cart) ? 'disabled' : '' }}>
                        Place Order
                    </button>

                </div>

            </div>

        </form>

    </div>
</div>

@endsection