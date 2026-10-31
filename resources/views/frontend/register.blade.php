@extends('layouts.website')

@section('title', 'Register')

@section('content')

<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Register</h1>

    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Home</a>
        </li>

        <li class="breadcrumb-item active text-white">
            Register
        </li>
    </ol>
</div>

<div class="container-fluid py-5">
    <div class="container py-5">

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        <h1 class="mb-4">
            Create Your Account
        </h1>

        <form action="{{ route('register.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="row g-5">

                <!-- LEFT SIDE -->
                <div class="col-md-12 col-lg-7">

                    <!-- ACCOUNT TYPE -->
                    <label class="form-label my-3">
                        Account Type *
                    </label>

                    <select name="type"
                        id="userType"
                        class="form-select @error('type') is-invalid @enderror">

                        <option value="{{ USER_TYPE_CUSTOMER }}"
                            {{ old('type', USER_TYPE_CUSTOMER) == USER_TYPE_CUSTOMER ? 'selected' : '' }}>
                            Customer
                        </option>

                        <option value="{{ USER_TYPE_DEALER }}"
                            {{ old('type') == USER_TYPE_DEALER ? 'selected' : '' }}>
                            Dealer
                        </option>

                        <option value="{{ USER_TYPE_AGENT }}"
                            {{ old('type') == USER_TYPE_AGENT ? 'selected' : '' }}>
                            Agent
                        </option>

                    </select>

                    @error('type')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                    @enderror


                    <!-- FIRST NAME -->
                    <label class="form-label my-3">
                        First Name *
                    </label>

                    <input type="text"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        class="form-control @error('first_name') is-invalid @enderror">

                    @error('first_name')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                    @enderror


                    <!-- LAST NAME -->
                    <label class="form-label my-3">
                        Last Name *
                    </label>

                    <input type="text"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        class="form-control @error('last_name') is-invalid @enderror">

                    @error('last_name')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                    @enderror


                    <!-- EMAIL -->
                    <label class="form-label my-3">
                        Email *
                    </label>

                    <input type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror">

                    @error('email')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                    @enderror


                    <!-- PHONE -->
                    <label class="form-label my-3">
                        Mobile *
                    </label>

                    <input type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                        class="form-control @error('phone') is-invalid @enderror">

                    @error('phone')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                    @enderror


                    <!-- ADDRESS -->
                    <label class="form-label my-3">
                        Address *
                    </label>

                    <input type="text"
                        name="address"
                        value="{{ old('address') }}"
                        class="form-control @error('address') is-invalid @enderror">

                    @error('address')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                    @enderror


                    <!-- PARTNER FIELDS -->
                    <div id="partnerFields" style="display: none;">

                        <!-- SHOP NAME -->
                        <label class="form-label my-3">
                            Shop Name *
                        </label>

                        <input type="text"
                            name="shop_name"
                            value="{{ old('shop_name') }}"
                            class="form-control @error('shop_name') is-invalid @enderror">

                        @error('shop_name')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                        @enderror


                        <!-- TRADE LICENSE -->
                        <label class="form-label my-3">
                            Trade License Number *
                        </label>

                        <input type="text"
                            name="trade_license"
                            value="{{ old('trade_license') }}"
                            class="form-control @error('trade_license') is-invalid @enderror">

                        @error('trade_license')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                        @enderror

                    </div>


                    <!-- PARTNER FILES -->
                    <div id="partnerFileFields" style="display: none;">

                        <!-- IMAGE -->
                        <label class="form-label my-3">
                            Photo *
                        </label>

                        <input type="file"
                            name="image"
                            class="form-control @error('image') is-invalid @enderror">

                        @error('image')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                        @enderror


                        <!-- NID -->
                        <label class="form-label my-3">
                            NID File *
                        </label>

                        <input type="file"
                            name="nid_file"
                            class="form-control @error('nid_file') is-invalid @enderror">

                        @error('nid_file')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                        @enderror


                        <!-- TRADE LICENSE FILE -->
                        <label class="form-label my-3">
                            Trade License File *
                        </label>

                        <input type="file"
                            name="trade_license_file"
                            class="form-control @error('trade_license_file') is-invalid @enderror">

                        @error('trade_license_file')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                        @enderror

                    </div>

                </div>

                <!-- RIGHT SIDE -->
                <div class="col-md-12 col-lg-5">

                    <div class="border p-4 rounded">

                        <h5 class="mb-3">
                            Account Information
                        </h5>

                        <p class="mb-2">
                            ✔ Create your account using checkout details
                        </p>

                        <p class="mb-2">
                            ✔ Fast login & order tracking
                        </p>

                        <p class="mb-2">
                            ✔ Secure system
                        </p>

                        <hr>

                        <!-- PASSWORD -->
                        <label class="form-label my-3">
                            Password *
                        </label>

                        <input type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror">

                        @error('password')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                        @enderror


                        <!-- CONFIRM PASSWORD -->
                        <label class="form-label my-3">
                            Confirm Password *
                        </label>

                        <input type="password"
                            name="password_confirmation"
                            class="form-control">


                        <!-- SUBMIT -->
                        <button type="submit"
                            class="btn btn-primary w-100 mt-4 py-3">

                            Register

                        </button>


                        <!-- LOGIN -->
                        <div class="text-center mt-3">

                            <a href="{{ route('login') }}"
                                class="text-primary">

                                Already have an account? Login

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePartnerFields() {

        let type = document.getElementById('userType').value;

        let partnerFields = document.getElementById('partnerFields');

        let partnerFileFields = document.getElementById('partnerFileFields');

        if (
            type == "{{ USER_TYPE_DEALER }}" ||
            type == "{{ USER_TYPE_AGENT }}"
        ) {

            partnerFields.style.display = 'block';

            partnerFileFields.style.display = 'block';

        } else {

            partnerFields.style.display = 'none';

            partnerFileFields.style.display = 'none';
        }
    }

    document.getElementById('userType')
        .addEventListener('change', togglePartnerFields);

    togglePartnerFields();
</script>
@endpush
