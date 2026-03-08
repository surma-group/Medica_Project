@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($brand) ? 'Brand Edit' : 'Brand Add' }}</h1>
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

    <form action="{{ isset($brand) ? route('brands.update', $brand->id) : route('brands.store') }}" 
          method="POST">
        @csrf
        @if(isset($brand))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Brand Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" 
                           value="{{ old('name', $brand->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control select2" required>
                        <option value="1" {{ old('status', $brand->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $brand->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> 
                {{ isset($brand) ? 'Update' : 'Submit' }}
            </button>
        </div>
    </form>
</section>
@endsection