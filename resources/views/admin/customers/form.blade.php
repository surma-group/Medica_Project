@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($customer) ? 'Customer Edit' : 'Customer Add' }}</h1>
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

    <form action="{{ isset($customer) ? route('customers.update', $customer->id) : route('customers.store') }}"
        method="POST">

        @csrf

        @if(isset($customer))
        @method('PUT')
        @endif

        <div class="row">

            <!-- LEFT -->
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $customer->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control"
                        value="{{ old('first_name', $customer->first_name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control"
                        value="{{ old('last_name', $customer->last_name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control"
                        value="{{ old('phone', $customer->phone ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                        value="{{ old('email', $customer->email ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control select2" required>
                        <option value="1" {{ old('status', $customer->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $customer->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

            </div>

            <!-- RIGHT -->
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control">{{ old('address', $customer->address ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Emergency Contact</label>
                    <input type="text" name="emergency_contact" class="form-control"
                        value="{{ old('emergency_contact', $customer->emergency_contact ?? '') }}">
                </div>

                <div class="form-group">
                    <label>District</label>

                    <select name="district_id" class="form-control select2">
                        <option value="">-- Select District --</option>

                        @foreach($districts as $district)
                        <option value="{{ $district->id }}"
                            {{ old('district_id', $customer->district_id ?? '') == $district->id ? 'selected' : '' }}>

                            {{ $district->name_en }} ({{ $district->name_bn }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Postcode</label>
                    <input type="text" name="postcode" class="form-control"
                        value="{{ old('postcode', $customer->postcode ?? '') }}">
                </div>

            </div>

        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($customer) ? 'Update' : 'Submit' }}
            </button>
        </div>

    </form>
</section>
@endsection