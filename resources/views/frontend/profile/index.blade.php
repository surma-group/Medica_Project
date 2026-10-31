@extends('layouts.website')

@section('title', 'My Profile - Medica')

@section('content')

<!-- Page Header -->
<div class="container-fluid page-header py-5">
    <div class="container text-center py-5">

        <h1 class="text-white display-6">My Account</h1>

        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Home</a>
            </li>

            <li class="breadcrumb-item">
                <a href="{{ route('profile') }}">Profile</a>
            </li>

            <li class="breadcrumb-item active text-white">
                My Account
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
                            class="list-group-item list-group-item-action {{ request()->routeIs('profile') ? 'active' : '' }}">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>

                        <a href="{{ route('profile.edit') }}"
                            class="list-group-item list-group-item-action {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                            <i class="fas fa-edit me-2"></i> Edit Profile
                        </a>

                        <a href="{{ route('profile.orders') }}"
                            class="list-group-item list-group-item-action {{ request()->routeIs('profile.orders') ? 'active' : '' }}">
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
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="bg-light rounded p-4 mb-4">
                    <h4 class="mb-4">Profile Details</h4>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Name:</strong></div>
                        <div class="col-md-8">{{ auth()->user()->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Email:</strong></div>
                        <div class="col-md-8">{{ auth()->user()->email }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Member Since:</strong></div>
                        <div class="col-md-8">
                            {{ auth()->user()->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-light rounded p-4">
                    <h4 class="mb-4">Recent Orders</h4>

                    <p class="text-muted">No orders found.</p>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection