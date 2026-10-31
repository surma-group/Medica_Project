@extends('layouts.website')

@section('title', 'Login')

@section('content')

<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Login</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Home</a>
        </li>
        <li class="breadcrumb-item active text-white">Login</li>
    </ol>
</div>

<div class="container-fluid py-5">
    <div class="container py-5">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- ERROR MESSAGE --}}
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form method="POST" action="{{ route('login.perform') }}">
            @csrf

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5 bg-light p-4 rounded shadow-sm">

                    {{-- EMAIL --}}
                    <div class="mb-3">
                        <label class="form-label">Email *</label>

                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror">

                        @error('email')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div class="mb-3">
                        <label class="form-label">Password *</label>

                        <input type="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror">

                        @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- REMEMBER --}}
                    <div class="form-check mb-3">
                        <input type="checkbox"
                               name="remember"
                               class="form-check-input"
                               id="remember"
                               {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            Remember Me
                        </label>
                    </div>

                    {{-- BUTTON --}}
                    <button type="submit" class="btn btn-primary w-100 py-3">
                        Login
                    </button>

                    {{-- REGISTER LINK --}}
                    <div class="text-center mt-3">
                        <a href="{{ route('register') }}" class="text-primary">
                            Don't have an account? Register here
                        </a>
                    </div>

                </div>
            </div>

        </form>

    </div>
</div>

@endsection