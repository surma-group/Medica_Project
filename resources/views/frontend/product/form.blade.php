@extends('layouts.website')

@section('title', 'Product Request')

@section('content')

<div class="container-fluid page-header py-5"
    style="background: url('{{ asset('website/img/cart-page-header-img.jpg') }}') center/cover no-repeat;">

    <h1 class="text-center text-white display-6">Product Request</h1>

    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Home</a>
        </li>

        <li class="breadcrumb-item">
            <a href="{{ route('shop') }}">Shop</a>
        </li>

        <li class="breadcrumb-item active text-white">
            Product Request
        </li>
    </ol>

</div>
<div class="container-fluid py-5">
    <div class="container py-5">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('product.request.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-5">

                <!-- LEFT SIDE -->
                <div class="col-md-12 col-lg-6">

                    <label class="form-label my-3">Image</label>
                    <input type="file" name="image" class="form-control">

                    <label class="form-label my-3">Product Name *</label>
                    <input type="text" name="name" class="form-control">

                    <label class="form-label my-3">Category *</label>
                    <select name="category_id" class="form-control select2">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label my-3">Company</label>
                    <select name="company_id" class="form-control select2">
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label my-3">Generic</label>
                    <select name="generic_id" class="form-control select2">
                        <option value="">Select Generic</option>
                        @foreach($generics as $generic)
                        <option value="{{ $generic->id }}">{{ $generic->generic_name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label my-3">Strength</label>
                    <input type="text" name="strength" class="form-control">

                </div>

                <!-- RIGHT SIDE -->
                <div class="col-md-12 col-lg-6">
                    <label class="form-label my-3">Generic</label>
                    <select name="generic_id" class="form-control select2">
                        <option value="">Select Generic</option>
                        @foreach($generics as $generic)
                        <option value="{{ $generic->id }}">{{ $generic->generic_name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label my-3">Manufacturer</label>
                    <input type="text" name="manufacturer_name" class="form-control">

                    <label class="form-label my-3">Description</label>
                    <textarea name="description" rows="5" class="form-control"></textarea>

                    <label class="form-label my-3">Barcode</label>
                    <input type="text"
                        name="barcode"
                        value="{{ $barcode }}"
                        class="form-control"
                        readonly>

                    <button class="btn btn-primary w-100 mt-4 py-3">
                        Submit Request
                    </button>

                </div>

            </div>

        </form>

    </div>
</div>

@endsection

@push('styles')
<style>
    .select2-container--default .select2-selection--single {
        height: 45px !important;
        border-radius: 10px !important;
        border: 1px solid #dcdcdc !important;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        background: #fff;
        transition: 0.3s;
    }

    .select2-container--default .select2-selection--single:hover,
    .select2-container--default .select2-selection--single:focus {
        border-color: #81c408 !important;
        box-shadow: 0 0 8px rgba(129, 196, 8, 0.2);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333;
        font-size: 14px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 45px;
        right: 10px;
    }

    .select2-dropdown {
        border-radius: 10px;
        border: 1px solid #ddd;
        overflow: hidden;
    }

    .select2-results__option--highlighted {
        background-color: #81c408 !important;
        color: #fff !important;
    }

    .select2-search--dropdown .select2-search__field {
        border-radius: 8px;
        padding: 6px;
    }

    .form-control {
        border-radius: 10px;
        height: 45px;
    }

    textarea.form-control {
        border-radius: 10px;
        height: auto !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: "Select option",
            allowClear: true
        });
    });
</script>
@endpush