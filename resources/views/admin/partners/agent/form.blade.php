@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($partner) ? 'Dealer Edit' : 'Dealer Add' }}</h1>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-has-icon">
        <div class="alert-icon">
            <i class="far fa-lightbulb"></i>
        </div>

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

    <form action="{{ isset($partner)
        ? route('partners.dealer.update', $partner->id)
        : route('partners.dealer.store') }}"
        method="POST">

        @csrf

        @if(isset($partner))
            @method('PUT')
        @endif

        <div class="row">

            <!-- LEFT SIDE -->
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $partner->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Shop Name</label>
                    <input type="text" name="shop_name" class="form-control"
                        value="{{ old('shop_name', $partner->shop_name ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control"
                        value="{{ old('phone', $partner->phone ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                        value="{{ old('email', $partner->email ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control select2" required>
                        <option value="1" {{ old('status', $partner->status ?? 1) == 1 ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0" {{ old('status', $partner->status ?? 1) == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>

            </div>

            <!-- RIGHT SIDE -->
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control">{{ old('address', $partner->address ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>District</label>
                    <select name="district_id" class="form-control select2">
                        <option value="">-- Select District --</option>

                        @foreach($districts as $district)
                        <option value="{{ $district->id }}"
                            {{ old('district_id', $partner->district_id ?? '') == $district->id ? 'selected' : '' }}>

                            {{ $district->name_en }} ({{ $district->name_bn }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Postcode</label>
                    <input type="text" name="postcode" class="form-control"
                        value="{{ old('postcode', $partner->postcode ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Trade License</label>
                    <input type="text" name="trade_license" class="form-control"
                        value="{{ old('trade_license', $partner->trade_license ?? '') }}">
                </div>

            </div>

        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($partner) ? 'Update' : 'Submit' }}
            </button>
        </div>

    </form>
</section>
@endsection