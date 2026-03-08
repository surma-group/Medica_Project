@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($supplier) ? 'Supplier Edit' : 'Supplier Add' }}</h1>
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

    <form action="{{ isset($supplier) ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}"
          method="POST">
        @csrf
        @if(isset($supplier))
            @method('PUT')
        @endif

        <div class="row">
            <!-- LEFT -->
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $supplier->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Mobile <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control"
                           value="{{ old('mobile', $supplier->mobile ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $supplier->email ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control select2">
                        <option value="1" {{ old('status', $supplier->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $supplier->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

            </div>

            <!-- RIGHT -->
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control">{{ old('address', $supplier->address ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" name="contact_person" class="form-control"
                           value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Contact Person Mobile</label>
                    <input type="text" name="contact_person_mobile" class="form-control"
                           value="{{ old('contact_person_mobile', $supplier->contact_person_mobile ?? '') }}">
                </div>

            </div>
        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($supplier) ? 'Update' : 'Submit' }}
            </button>
        </div>
    </form>
</section>
@endsection