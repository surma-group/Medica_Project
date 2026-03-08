@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($salaryComponent) ? 'Edit Salary Component' : 'Add Salary Component' }}</h1>
    </div>

    {{-- Display Validation Errors --}}
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

    <form action="{{ isset($salaryComponent) ? route('salary_components.update', $salaryComponent->id) : route('salary_components.store') }}" method="POST">
        @csrf
        @if(isset($salaryComponent))
        @method('PUT')
        @endif

        <div class="row">
            <div class="col-12 col-md-6">
                {{-- Title --}}
                <div class="form-group">
                    <label>Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title', $salaryComponent->title ?? '') }}" required>
                    <div class="invalid-feedback">Title is required.</div>
                </div>

                {{-- Amount Type --}}
                <div class="form-group">
                    <label>Amount Type <span class="text-danger">*</span></label>
                    <select name="amount_type" class="form-control select2" required>
                        <option value="">-- Select Amount Type --</option>
                        <option value="1" {{ old('amount_type', $salaryComponent->amount_type ?? '') == 1 ? 'selected' : '' }}>Flat</option>
                        <option value="2" {{ old('amount_type', $salaryComponent->amount_type ?? '') == 2 ? 'selected' : '' }}>Percentage</option>
                    </select>
                    <div class="invalid-feedback">Amount type is required.</div>
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control select2">
                        <option value="1" {{ old('status', $salaryComponent->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $salaryComponent->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-6">
                {{-- Payment Type --}}
                <div class="form-group">
                    <label>Payment Type <span class="text-danger">*</span></label>
                    <select name="payment_type" class="form-control select2" required>
                        <option value="">-- Select Payment Type --</option>
                        <option value="1" {{ old('payment_type', $salaryComponent->payment_type ?? '') == 1 ? 'selected' : '' }}>Earning</option>
                        <option value="2" {{ old('payment_type', $salaryComponent->payment_type ?? '') == 2 ? 'selected' : '' }}>Deduction</option>
                    </select>
                    <div class="invalid-feedback">Payment type is required.</div>
                </div>

                {{-- Amount --}}
                <div class="form-group">
                    <label id="amount_label">
                        {{ (old('amount_type', $salaryComponent->amount_type ?? 1) == 2) ? 'Percentage (%)' : 'Amount' }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                        step="0.01"
                        name="amount"
                        class="form-control"
                        value="{{ old('amount', $salaryComponent->amount ?? '') }}"
                        required>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($salaryComponent) ? 'Update' : 'Submit' }}
            </button>
        </div>
    </form>
</section>
@endsection