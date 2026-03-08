@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($branch) ? 'Branch Edit' : 'Branch Add' }}</h1>
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
    <form action="{{ isset($branch) ? route('branch.update', $branch->id) : route('branch.store') }}" 
          method="POST">
        @csrf
        @if(isset($branch))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Company <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-control select2" required>
                        <option value="">--Select Company--</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" 
                                {{ old('company_id', $branch->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Branch Code <span class="text-danger">*</span></label>
                    <input type="text" name="branch_code" class="form-control" 
                           value="{{ old('branch_code', $branch->branch_code ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Is Head Office <span class="text-danger">*</span></label>
                    <select name="is_head_office" class="form-control select2" required>
                        <option value="0" {{ old('is_head_office', $branch->is_head_office ?? 0) == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('is_head_office', $branch->is_head_office ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" 
                           value="{{ old('email', $branch->email ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" name="mobile" class="form-control" 
                           value="{{ old('mobile', $branch->mobile ?? '') }}">
                </div>

            </div>

            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Branch Name <span class="text-danger">*</span></label>
                    <input type="text" name="branch_name" class="form-control" 
                           value="{{ old('branch_name', $branch->branch_name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>District <span class="text-danger">*</span></label>
                    <select name="district" class="form-control select2" required>
                        <option value="">--Select District--</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" 
                                {{ old('district', $branch->district ?? '') == $district->id ? 'selected' : '' }}>
                                {{ $district->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Opening Date</label>
                    <input type="date" name="opening_date" class="form-control" value="{{ old('opening_date', isset($branch->opening_date) ? $branch->opening_date->format('Y-m-d') : '') }}">

                </div>

                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control select2" required>
                        <option value="1" {{ old('status', $branch->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $branch->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control">{{ old('address', $branch->address ?? '') }}</textarea>
                </div>

            </div>
        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> 
                {{ isset($branch) ? 'Update' : 'Submit' }}
            </button>
        </div>
    </form>
</section>
@endsection
