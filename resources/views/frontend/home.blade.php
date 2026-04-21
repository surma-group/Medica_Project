@extends('layouts.website')

@section('title', 'Home - Medica')

@section('content')

<!-- Hero Start -->
<div class="container-fluid py-5 mb-5 hero-header">
    <div class="container py-5">
        <div class="row g-5 align-items-center">

            {{-- Left Content --}}
            <div class="col-md-12 col-lg-7">
                <h4 class="mb-3 text-secondary">100% Organic Foods</h4>
                <h1 class="mb-5 display-3 text-primary">Organic Veggies & Fruits Foods</h1>

                <div class="position-relative mx-auto">
                    <input class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill" type="text" placeholder="Search">
                    <button type="submit"
                        class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100"
                        style="top: 0; right: 25%;">
                        Submit Now
                    </button>
                </div>
            </div>

            {{-- Slider --}}
            <div class="col-md-12 col-lg-5">
                <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                    <div class="carousel-inner">

                        <div class="carousel-item active rounded">
                            <img src="{{ asset('website/img/hero-img-1.png') }}" class="img-fluid w-100 h-100 bg-secondary rounded" alt="">
                            <a href="#" class="btn px-4 py-2 text-white rounded">Fruits</a>
                        </div>

                        <div class="carousel-item rounded">
                            <img src="{{ asset('website/img/hero-img-2.jpg') }}" class="img-fluid w-100 h-100 rounded" alt="">
                            <a href="#" class="btn px-4 py-2 text-white rounded">Vegetables</a>
                        </div>

                    </div>

                    <button class="carousel-control-prev" type="button"
                        data-bs-target="#carouselId" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>

                    <button class="carousel-control-next" type="button"
                        data-bs-target="#carouselId" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>

                </div>
            </div>

        </div>
    </div>
</div>
<!-- Hero End -->


<!-- Vesitable Shop Start-->
<div class="container-fluid vesitable py-5">
    <div class="container py-5">
        <h1 class="mb-0">Products</h1>

        <div class="owl-carousel vegetable-carousel justify-content-center">

            @foreach($products as $product)

            <div class="border border-primary rounded position-relative vesitable-item">

                {{-- IMAGE --}}
                <div class="vesitable-img" style="height: 220px; overflow: hidden;">
                    <img
                        src="{{ $product->image 
                ? asset('storage/'.$product->image) 
                : asset('website/img/product.jpg') }}"
                        class="img-fluid w-100 h-100"
                        style="object-fit: cover;"
                        alt="{{ $product->name }}">
                </div>

                <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                    style="top:10px; right:10px;">
                    {{ $product->category->name ?? 'Product' }}
                </div>

                <div class="p-4 rounded-bottom">
                    <h4>{{ $product->name }}</h4>

                    <p>{{ $product->strength }}</p>

                    <div class="d-flex justify-content-between">
                        <p class="fw-bold">${{ $product->price }}</p>

                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf

                            <button type="submit" class="btn border rounded-pill text-primary">
                                Add to cart
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            @endforeach

        </div>
        {{-- VIEW ALL BUTTON --}}
        <div class="text-center mt-4">
            <a href="{{ route('shop') }}"
                class="btn btn-primary rounded-pill px-5 py-2">
                View All Products
            </a>
        </div>
    </div>
</div>
<!-- Vesitable Shop End-->


<!-- Features Start -->
<div class="container-fluid featurs py-5">
    <div class="container py-5">
        <div class="row g-4">

            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-car-side fa-3x text-white"></i>
                    </div>
                    <h5>Free Shipping</h5>
                    <p class="mb-0">Free on order over $300</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-user-shield fa-3x text-white"></i>
                    </div>
                    <h5>Security Payment</h5>
                    <p class="mb-0">100% security payment</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-exchange-alt fa-3x text-white"></i>
                    </div>
                    <h5>30 Day Return</h5>
                    <p class="mb-0">30 day money guarantee</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fa fa-phone-alt fa-3x text-white"></i>
                    </div>
                    <h5>24/7 Support</h5>
                    <p class="mb-0">Support every time fast</p>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Features End -->

<!-- Best Seller Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <h1 class="text-center mb-5">Bestseller Products</h1>

        <div class="row g-4">

            @for($i=1; $i<=6; $i++)
                <div class="col-lg-6 col-xl-4">
                <div class="p-4 rounded bg-light">
                    <div class="row align-items-center">

                        <div class="col-6">
                            <img src="{{ asset('website/img/best-product-'.$i.'.jpg') }}"
                                class="img-fluid rounded-circle w-100">
                        </div>

                        <div class="col-6">
                            <h5>Organic Tomato</h5>
                            <div class="mb-2 text-primary">
                                ★★★★☆
                            </div>
                            <h4>$3.12</h4>
                            <a href="#" class="btn border rounded-pill text-primary">
                                Add to cart
                            </a>
                        </div>

                    </div>
                </div>
        </div>
        @endfor

    </div>
</div>
</div>
<!-- Best Seller End -->

@endsection