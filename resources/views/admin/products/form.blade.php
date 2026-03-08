@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($product) ? 'Edit Product' : 'Add Product' }}</h1>
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

    <form action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($product))
        @method('PUT')
        @endif

        <div class="row">

            <!-- Left Column -->
            <div class="col-12 col-md-6">
                <!-- Product Name -->
                <div class="form-group">
                    <label>Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $product->name ?? '') }}" required>
                </div>

                <!-- Barcode -->
                <div class="form-group">
                    <label>Barcode <span class="text-danger">*</span></label>
                    <input type="text" name="barcode" class="form-control"
                        value="{{ old('barcode', $product->barcode ?? $barcode ?? '') }}" required>
                </div>

                <!-- Generic Name -->
                <div class="form-group">
                    <label>Generic Name</label>
                    <input type="text" name="generic_name" class="form-control"
                        value="{{ old('generic_name', $product->generic_name ?? '') }}">
                </div>

                <!-- Strength -->
                <div class="form-group">
                    <label>Strength</label>
                    <input type="text" name="strength" class="form-control"
                        value="{{ old('strength', $product->strength ?? '') }}">
                </div>

                <!-- Manufacturer Name -->
                <div class="form-group">
                    <label>Manufacturer Name </label>
                    <input type="text" name="manufacturer_name" class="form-control"
                        value="{{ old('manufacturer_name', $product->manufacturer_name ?? '') }}">
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-12 col-md-6">
                <!-- Category -->
                <div class="form-group">
                    <label>Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-control select2" required>
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
                    <label>Brand <span class="text-danger">*</span></label>
                    <select name="brand_id" class="form-control select2" required>
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}"
                            {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control select2">
                        <option value="1" {{ old('status', $product->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $product->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Product Image -->
                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="image" class="form-control">
                    @if(isset($product) && $product->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $product->image) }}"
                            alt="{{ $product->name }}" width="120" class="rounded">
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($product) ? 'Update' : 'Submit' }}
            </button>
        </div>
    </form>
</section>
@endsection