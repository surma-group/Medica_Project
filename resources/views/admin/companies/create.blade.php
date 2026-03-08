@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($company) ? 'Company Edit' : 'Company Add' }}</h1>
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
    <form action="{{ isset($company) ? route('company.update', $company->id) : route('company.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($company))
        @method('PUT')
        @endif

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Company Code <span class="text-danger">*</span></label>
                    <input type="text" name="company_code" class="form-control" value="{{ old('company_code', $company->company_code ?? '') }}" required>
                    <div class="invalid-feedback">Company code is required.</div>
                </div>
                <div class="form-group">
                    <label>Tag line <span class="text-danger">*</span></label>
                    <input type="text" name="tag_line" class="form-control" value="{{ old('tag_line', $company->tag_line ?? '') }}" required>
                    <div class="invalid-feedback">Tag line is required.</div>
                </div>
                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $company->mobile ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Website</label>
                    <input type="text" name="website" class="form-control" value="{{ old('website', $company->website ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Timezone</label>
                    <select name="timezone_id" class="form-control select2">
                        @foreach($timezones as $timezone)
                        <option value="{{ $timezone->id }}" {{ old('timezone_id', $company->timezone_id ?? $defaultTimezoneId) == $timezone->id ? 'selected' : '' }}>
                            {{ $timezone->name }} ({{ $timezone->utc_offset }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>District</label>
                    <select name="district_id" class="form-control select2">
                        <option value="">--Select District--</option>
                        @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ old('district_id',1, $company->district_id ?? '') == $district->id ? 'selected' : '' }}>
                            {{ $district->name_en }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <!-- <div class="form-group">
                    <label>Short Name</label>
                    <input type="text" name="company_short_name" class="form-control" value="{{ old('company_short_name', $company->company_short_name ?? '') }}">
                </div> -->



                <!-- <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone ?? '') }}">
                </div> -->
                <div class="form-group">
                    <label>Logo</label>
                    <input type="file" name="logo" class="form-control">
                    @if(isset($company) && $company->logo)
                    <img
                        src="{{ asset('storage/app/public/'.$company->logo) }}"
                        alt="Company Logo"
                        class="img-thumbnail mt-2"
                        style="max-width: 150px;">
                    @endif
                </div>

            </div>

            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $company->company_name ?? '') }}" required>
                    <div class="invalid-feedback">Company name is required.</div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ old('description', $company->description ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $company->email ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Currency</label>
                    <select name="currency_id" class="form-control select2">
                        @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" {{ old('currency_id', $company->currency_id ?? $defaultCurrencyId) == $currency->id ? 'selected' : '' }}>
                            {{ $currency->code }}
                        </option>
                        @endforeach
                    </select>
                </div>



                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control select2">
                        <option value="1" {{ old('status', $company->status ?? $defaultStatus) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $company->status ?? $defaultStatus) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address_line_1" class="form-control">{{ old('address_line_1', $company->address_line_1 ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Favicon</label>
                    <input type="file" name="favicon" class="form-control">
                    @if(isset($company) && $company->favicon)
                    <img src="{{ asset('storage/' . $company->favicon) }}" alt="Favicon" class="img-thumbnail mt-2" style="max-width: 50px;">
                    @endif
                </div>
                <!-- <div class="form-group">
                    <label>Address Line 2</label>
                    <textarea name="address_line_2" class="form-control">{{ old('address_line_2', $company->address_line_2 ?? '') }}</textarea>
                </div> -->
            </div>
        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($company) ? 'Update' : 'Submit' }}
            </button>
        </div>
    </form>
</section>
@endsection