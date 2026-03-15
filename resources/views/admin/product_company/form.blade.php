@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($product_company) ? 'Product Company Edit' : 'Product Company Add' }}</h1>
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

    <form action="{{ isset($product_company) ? route('product_company.update', $product_company->id) : route('product_company.store') }}" 
          method="POST">
        @csrf

        @if(isset($product_company))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Company Name <span class="text-danger">*</span></label>
                    <input type="text"
                           name="company_name"
                           class="form-control"
                           value="{{ old('company_name', $product_company->company_name ?? '') }}"
                           required>
                </div>

                <div class="form-group">
                    <label>Company Order</label>
                    <input type="number"
                           name="company_order"
                           class="form-control"
                           value="{{ old('company_order', $product_company->company_order ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control select2" required>
                        <option value="1" {{ old('status', $product_company->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $product_company->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($product_company) ? 'Update' : 'Submit' }}
            </button>
        </div>

    </form>
</section>
@endsection