@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($paymentAccount) ? 'Payment Account Edit' : 'Payment Account Add' }}</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-has-icon">
            <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
            <div class="alert-body">
                <div class="alert-title">Whoops! Something went wrong.</div>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ isset($paymentAccount) ? route('payment-accounts.update', $paymentAccount->id) : route('payment-accounts.store') }}"
          method="POST">

        @csrf

        @if(isset($paymentAccount))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-12 col-md-6">

                <!-- Name -->
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $paymentAccount->name ?? '') }}" required>
                </div>

                <!-- Type -->
                <div class="form-group">
                    <label>Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-control select2" required>
                        <option value="1" {{ old('type', $paymentAccount->type ?? 1) == 1 ? 'selected' : '' }}>Personal</option>
                        <option value="2" {{ old('type', $paymentAccount->type ?? 1) == 2 ? 'selected' : '' }}>Agent</option>
                        <option value="3" {{ old('type', $paymentAccount->type ?? 1) == 3 ? 'selected' : '' }}>Merchant</option>
                    </select>
                </div>

                <!-- Number -->
                <div class="form-group">
                    <label>Account Number <span class="text-danger">*</span></label>
                    <input type="text" name="number" class="form-control"
                           value="{{ old('number', $paymentAccount->number ?? '') }}" required>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control select2" required>
                        <option value="1" {{ old('status', $paymentAccount->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $paymentAccount->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($paymentAccount) ? 'Update' : 'Submit' }}
            </button>
        </div>

    </form>
</section>
@endsection