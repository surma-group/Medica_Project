@extends('layouts.website')

@section('title', 'Shop - SURMA GROUP')

@section('content')

<div class="container-fluid page-header py-5"
    style="background: url('{{ asset('website/img/cart-page-header-img.jpg') }}') center/cover no-repeat;">
    <h1 class="text-center text-white display-6">Shop</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Home</a>
        </li>
        <li class="breadcrumb-item active text-white">Shop</li>
    </ol>
</div>

<div class="container-fluid fruite py-5">
    <div class="container py-5">
        <h1 class="mb-4">Medicine shop</h1>
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="row g-4">
                    <div class="col-xl-3">
                        <div class="input-group w-100 mx-auto d-flex">
                            <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                            <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                    <div class="col-6"></div>
                    <div class="col-xl-3">
                        <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                            <label for="fruits">Default Sorting:</label>
                            <select id="fruits" name="fruitlist" class="border-0 form-select-sm bg-light me-3" form="fruitform">
                                <option value="volvo">Nothing</option>
                                <option value="saab">Popularity</option>
                                <option value="opel">Organic</option>
                                <option value="audi">Fantastic</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-3">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h4>Categories</h4>

                                    <form method="GET" action="{{ url('shop') }}">

                                        {{-- ALL option --}}
                                        <div class="mb-2">
                                            <input type="radio"
                                                class="me-2"
                                                id="cat-all"
                                                name="category"
                                                value=""
                                                onchange="this.form.submit()"
                                                {{ request('category') == null ? 'checked' : '' }}>

                                            <label for="cat-all">All</label>
                                        </div>

                                        {{-- Dynamic categories --}}
                                        @foreach($categories as $category)
                                        <div class="mb-2">
                                            <input type="radio"
                                                class="me-2"
                                                id="cat-{{ $category->id }}"
                                                name="category"
                                                value="{{ $category->id }}"
                                                onchange="this.form.submit()"
                                                {{ request('category') == $category->id ? 'checked' : '' }}>

                                            <label for="cat-{{ $category->id }}">
                                                {{ $category->name }}
                                                ({{ $category->products_count ?? 0 }})
                                            </label>
                                        </div>
                                        @endforeach

                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <h4>Companies</h4>

                                    <ul class="list-unstyled fruite-categorie">

                                        @forelse($companies as $company)
                                        <li>
                                            <div class="d-flex justify-content-between fruite-name">

                                                <a href="{{ url('shop?company='.$company->id) }}"
                                                    class="{{ request('company') == $company->id ? 'fw-bold text-success' : '' }}">

                                                    <i class="fas fa-building me-2"></i>
                                                    {{ $company->company_name }}
                                                </a>

                                                <span>({{ $company->products_count }})</span>

                                            </div>
                                        </li>
                                        @empty
                                        <li>No companies found</li>
                                        @endforelse

                                    </ul>

                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="position-relative">
                                    <img src="{{ asset('website/img/banner-fruits.jpg') }}" class="img-fluid w-100 rounded" alt="">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="row g-4 justify-content-center">
                            @forelse($products as $product)
                            <div class="col-md-6 col-lg-6 col-xl-4 d-flex">
                                <div class="rounded position-relative fruite-item w-100 h-100 d-flex flex-column">

                                    {{-- IMAGE --}}
                                    <div class="fruite-img" style="height: 220px; overflow: hidden;">
                                        <img
                                            src="{{ $product->image 
                                ? asset('storage/'.$product->image) 
                                : asset('website/img/product.jpg') }}"
                                            class="img-fluid w-100 h-100"
                                            style="object-fit: cover;"
                                            alt="{{ $product->name }}">
                                    </div>

                                    {{-- CATEGORY --}}
                                    <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                        style="top: 10px; left: 10px;">
                                        {{ $product->category->name ?? 'Uncategorized' }}
                                    </div>

                                    {{-- CONTENT --}}
                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom flex-grow-1 d-flex flex-column">

                                        <h4 class="mb-2">{{ $product->name }}</h4>

                                        <p class="flex-grow-1">
                                            {{ \Illuminate\Support\Str::limit($product->strength ?? 'No description available', 80) }}
                                        </p>

                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <p class="text-dark fs-5 fw-bold mb-0">
                                                ${{ $product->price }}
                                            </p>

                                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                @csrf

                                                <button type="submit"
                                                    class="btn border border-secondary rounded-pill px-3 text-primary">
                                                    <i class="fa fa-shopping-bag me-2 text-primary"></i>
                                                    Add to cart
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <h4 class="text-center">No products found.</h4>
                            </div>
                            @endforelse

                            @if ($products->hasPages())
                            <div class="col-12">
                                <div class="pagination d-flex justify-content-center mt-5">

                                    {{-- Prev --}}
                                    @if ($products->onFirstPage())
                                    <span class="rounded disabled">&laquo;</span>
                                    @else
                                    <a href="{{ $products->previousPageUrl() }}" class="rounded">&laquo;</a>
                                    @endif

                                    {{-- First page --}}
                                    @if ($products->currentPage() > 3)
                                    <a href="{{ $products->url(1) }}" class="rounded">1</a>

                                    @if ($products->currentPage() > 4)
                                    <span class="rounded">...</span>
                                    @endif
                                    @endif

                                    {{-- Middle pages (smart range) --}}
                                    @foreach (range(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page)
                                    @if ($page == $products->currentPage())
                                    <span class="active rounded">{{ $page }}</span>
                                    @else
                                    <a href="{{ $products->url($page) }}" class="rounded">{{ $page }}</a>
                                    @endif
                                    @endforeach

                                    {{-- Last page --}}
                                    @if ($products->currentPage() < $products->lastPage() - 2)
                                        @if ($products->currentPage() < $products->lastPage() - 3)
                                            <span class="rounded">...</span>
                                            @endif

                                            <a href="{{ $products->url($products->lastPage()) }}" class="rounded">
                                                {{ $products->lastPage() }}
                                            </a>
                                            @endif

                                            {{-- Next --}}
                                            @if ($products->hasMorePages())
                                            <a href="{{ $products->nextPageUrl() }}" class="rounded">&raquo;</a>
                                            @else
                                            <span class="rounded disabled">&raquo;</span>
                                            @endif

                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pagination .rounded {
        padding: 8px 14px;
        border: 1px solid #ddd;
        margin: 0 3px;
        text-decoration: none;
        color: #333;
    }

    .pagination .active {
        background: #81c408;
        color: white;
        border: 1px solid #81c408;
    }

    .pagination .disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    .pagination .rounded:hover {
        background: #81c408;
        color: #fff;
        border-color: #81c408;
    }
</style>
@endpush