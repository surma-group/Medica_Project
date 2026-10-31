@extends('layouts.website')

@section('title', 'Edit Profile - Medica')

@section('content')

<!-- Page Header -->
<div class="container-fluid page-header py-5">
    <div class="container text-center py-5">

        <h1 class="text-white display-6">Edit Profile</h1>

        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Home</a>
            </li>

            <li class="breadcrumb-item">
                <a href="{{ route('profile') }}">Profile</a>
            </li>

            <li class="breadcrumb-item active text-white">
                Edit Profile
            </li>
        </ol>

    </div>
</div>

<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row g-4">

            <!-- LEFT SIDEBAR -->
            <div class="col-lg-3">
                <div class="bg-light rounded p-4">

                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                        <h5 class="mt-3">{{ auth()->user()->name }}</h5>
                    </div>

                    <div class="list-group">

                        <a href="{{ route('profile') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>

                        <a href="{{ route('profile.edit') }}"
                            class="list-group-item list-group-item-action active">
                            <i class="fas fa-edit me-2"></i> Edit Profile
                        </a>

                        <a href="{{ route('profile.orders') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-box me-2"></i> My Orders
                        </a>

                        <a href="{{ route('logout') }}"
                            class="list-group-item list-group-item-action text-danger"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>

                    </div>

                </div>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="col-lg-9">

                <div class="bg-light rounded p-4">

                    <h4 class="mb-4">Edit Profile</h4>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- PROFILE UPDATE FORM -->
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', auth()->user()->name) }}"
                                required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', auth()->user()->email) }}"
                                required>
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text"
                                name="phone"
                                class="form-control"
                                value="{{ old('phone', auth()->user()->phone) }}">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Profile
                        </button>

                    </form>

                    <hr class="my-4">

                    <!-- PASSWORD CHANGE -->
                    <h4 class="mb-3">Change Password</h4>

                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password"
                                name="current_password"
                                class="form-control"
                                required>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password"
                                name="new_password"
                                class="form-control"
                                required>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password"
                                name="new_password_confirmation"
                                class="form-control"
                                required>
                        </div>

                        <button type="submit" class="btn btn-danger">
                            Change Password
                        </button>

                    </form>

                </div>

            </div>

        </div>
    </div>
</div>

@endsection