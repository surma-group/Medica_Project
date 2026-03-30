@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($product) ? 'Edit Product' : 'Add Product' }}</h1>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($product))
        @method('PUT')
        @endif

        <div class="row">

            <!-- Left -->
            <div class="col-md-6">

                <!-- Name -->
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $product->name ?? '') }}" required>
                </div>

                <!-- Barcode -->
                <div class="form-group">
                    <label>Barcode</label>
                    <input type="text" name="barcode" class="form-control"
                        value="{{ old('barcode', $product->barcode ?? $barcode ?? '') }}">
                </div>

                <!-- Generic -->
                <div class="form-group">
                    <label>Generic</label>
                    <select name="generic_id" class="form-control select2">
                        <option value="">Select Generic</option>
                        @foreach($generics as $generic)
                        <option value="{{ $generic->id }}"
                            {{ old('generic_id', $product->generic_id ?? '') == $generic->id ? 'selected' : '' }}>
                            {{ $generic->generic_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Strength -->
                <div class="form-group">
                    <label>Strength</label>
                    <input type="text" name="strength" class="form-control"
                        value="{{ old('strength', $product->strength ?? '') }}">
                </div>

                <!-- Manufacturer -->
                <div class="form-group">
                    <label>Manufacturer Name</label>
                    <input type="text" name="manufacturer_name" class="form-control"
                        value="{{ old('manufacturer_name', $product->manufacturer_name ?? '') }}">
                </div>

                <!-- Price -->
                <div class="form-group">
                    <label>Price *</label>
                    <input type="number" step="0.01" name="price" class="form-control"
                        value="{{ old('price', $product->price ?? 0) }}" required>
                </div>

            </div>

            <!-- Right -->
            <div class="col-md-6">

                <!-- Category -->
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control select2">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand -->
                <div class="form-group">
                    <label>Brand</label>
                    <select name="brand_id" class="form-control select2">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}"
                            {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Company -->
                <div class="form-group">
                    <label>Company</label>
                    <select name="company_id" class="form-control select2">
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}"
                            {{ old('company_id', $product->company_id ?? '') == $company->id ? 'selected' : '' }}>
                            {{ $company->company_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status', $product->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $product->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Image -->
                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="image" class="form-control">

                    @if(isset($product) && $product->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $product->image) }}"
                            width="120" class="rounded">
                    </div>
                    @endif
                </div>

            </div>

        </div>

        <div class="text-end mt-3">
            <button class="btn btn-primary">
                {{ isset($product) ? 'Update' : 'Submit' }}
            </button>
        </div>

    </form>
</section>
@endsection