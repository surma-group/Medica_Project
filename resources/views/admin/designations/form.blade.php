@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($designation) ? 'Designation Edit' : 'Designation Add' }}</h1>
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
    <form action="{{ isset($designation) ? route('designation.update', $designation->id) : route('designation.store') }}"
          method="POST">
        @csrf
        @if(isset($designation))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-12 col-md-6">

                <!-- Designation Name -->
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $designation->name ?? '') }}"
                           required>
                    <div class="invalid-feedback">Name is required.</div>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control select2">
                        <option value="1" {{ old('status', $designation->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $designation->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

            </div>

            <div class="col-12 col-md-6">

                <!-- Code -->
                <div class="form-group">
                    <label>Code <span class="text-danger">*</span></label>
                    <input type="text"
                           name="code"
                           class="form-control"
                           value="{{ old('code', $designation->code ?? '') }}"
                           required>
                    <div class="invalid-feedback">Code is required.</div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"
                              class="form-control">{{ old('description', $designation->description ?? '') }}</textarea>
                </div>

            </div>
        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($designation) ? 'Update' : 'Submit' }}
            </button>
        </div>
    </form>
</section>
@endsection
