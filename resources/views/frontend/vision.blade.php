@extends('layouts.website')

@section('title', 'Home - SURMA GROUP')

@section('content')

<!-- Page Title -->
<div class="page-title dark-background" style="background-image: url('{{ asset('website/assets/img/travel/showcase-12.webp') }}');">
    <div class="container position-relative">
        <h1>Our Vision</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="current">Vision</li>
            </ol>
        </nav>
    </div>
</div><!-- End Page Title -->

<section id="travel-destination-details" class="travel-destination-details section">

    <div class="container">
        <!-- Overview Section -->
        <div class="destination-overview">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="overview-content">
                        <h2>আমাদের ভিশন (Vision)</h2>
                        <p>একটি টেকসই, নৈতিক ও আন্তর্জাতিক মানসম্পন্ন ব্যবসায়িক গ্রুপ হিসেবে দেশের অর্থনৈতিক উন্নয়নে অগ্রণী ভূমিকা পালন করা এবং বৈশ্বিক পর্যায়ে একটি সম্মানজনক ব্র্যান্ড হিসেবে প্রতিষ্ঠিত হওয়া।</p>
                        <p>To play a leading role in the country’s economic development as a sustainable, ethical, and internationally standard business group, and to establish itself as a reputable brand at the global level.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="overview-image">
                        <img src="{{ asset('website/assets/img/articles.webp') }}" alt="Bali culture" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>


    </div>

</section>

@endsection