@extends('layouts.website')

@section('title', 'Home')

@section('content')

<!-- Hero Section Begin -->
<section class="hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="hero__categories">
                    <div class="hero__categories__all">
                        <i class="fa fa-bars"></i>
                        <span>All Brands</span>
                    </div>
                    <ul>
                        @foreach ($brands as $brand)
                            <li><a href="#">{{ $brand->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="hero__search">
                    <div class="hero__search__form">
                        <form action="#">
                            <input type="text" placeholder="What do you need?">
                            <button type="submit" class="site-btn">SEARCH</button>
                        </form>
                    </div>

                    

                    <div class="hero__search__phone">
                        <div class="hero__search__phone__icon">
                            <i class="fa fa-phone"></i>
                        </div>
                        <div class="hero__search__phone__text">
                            <h5>+880 1312 468813</h5>
                            <span>support 24/7 time</span>
                        </div>
                    </div>
                </div>

                <div class="hero__item set-bg" data-setbg="{{ asset('website/assets/img/hero/banner.jpg') }}">
                    <div class="hero__text">
                        <span>FRUIT FRESH</span>
                        <h2>Vegetable <br>100% Organic</h2>
                        <p>Free Pickup and Delivery Available</p>
                        <a href="#" class="primary-btn">SHOP NOW</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Hero Section End -->


<!-- Categories Section Begin -->
<section class="categories">
    <div class="container">
        <div class="row">
            <div class="categories__slider owl-carousel">

                <div class="col-lg-3">
                    <div class="categories__item set-bg" data-setbg="{{ asset('website/assets/img/categories/cat-1.jpg') }}">
                        <h5><a href="#">Fresh Fruit</a></h5>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="categories__item set-bg" data-setbg="{{ asset('website/assets/img/categories/cat-2.jpg') }}">
                        <h5><a href="#">Dried Fruit</a></h5>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="categories__item set-bg" data-setbg="{{ asset('website/assets/img/categories/cat-3.jpg') }}">
                        <h5><a href="#">Vegetables</a></h5>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="categories__item set-bg" data-setbg="{{ asset('website/assets/img/categories/cat-4.jpg') }}">
                        <h5><a href="#">Drink Fruits</a></h5>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="categories__item set-bg" data-setbg="{{ asset('website/assets/img/categories/cat-5.jpg') }}">
                        <h5><a href="#">Fresh Vegetables</a></h5>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!-- Categories Section End -->


<!-- Featured Section Begin -->
<section class="featured spad">
    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>Featured Product</h2>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="featured__item">
                    <div class="featured__item__pic set-bg"
                        data-setbg="{{ asset('website/assets/img/featured/feature-1.jpg') }}">
                        <ul class="featured__item__pic__hover">
                            <li><a href="#"><i class="fa fa-heart"></i></a></li>
                            <li><a href="#"><i class="fa fa-retweet"></i></a></li>
                            <li><a href="#"><i class="fa fa-shopping-cart"></i></a></li>
                        </ul>
                    </div>

                    <div class="featured__item__text">
                        <h6><a href="#">Crab Pool Security</a></h6>
                        <h5>$30.00</h5>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
<!-- Featured Section End -->


<!-- Banner Begin -->
<div class="banner">
    <div class="container">
        <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="banner__pic">
                    <img src="{{ asset('website/assets/img/banner/banner-1.jpg') }}" alt="">
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="banner__pic">
                    <img src="{{ asset('website/assets/img/banner/banner-2.jpg') }}" alt="">
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Banner End -->


<!-- Blog Section Begin -->
<section class="from-blog spad">
    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="section-title from-blog__title">
                    <h2>From The Blog</h2>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic">
                        <img src="{{ asset('website/assets/img/blog/blog-1.jpg') }}" alt="">
                    </div>
                    <div class="blog__item__text">
                        <ul>
                            <li><i class="fa fa-calendar-o"></i> May 4,2019</li>
                            <li><i class="fa fa-comment-o"></i> 5</li>
                        </ul>
                        <h5><a href="#">Cooking tips make cooking simple</a></h5>
                        <p>Sed quia non numquam modi tempora indunt ut labore.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
<!-- Blog Section End -->

@endsection 