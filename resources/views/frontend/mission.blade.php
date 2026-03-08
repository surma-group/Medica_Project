@extends('layouts.website')

@section('title', 'Home - SURMA GROUP')

@section('content')

<!-- Page Title -->
<div class="page-title dark-background" style="background-image: url('{{ asset('website/assets/img/travel/showcase-12.webp') }}');">
    <div class="container position-relative">
        <h1>Our Mission</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="current">Mission</li>
            </ol>
        </nav>
    </div>
</div><!-- End Page Title -->

<section id="travel-destination-details" class="travel-destination-details section">

    <div class="container">
        <!-- Practical Information Section -->
        <div class="practical-info">
            <div class="section-header">
                <h2>Our Mission</h2>
                <p>To provide a safe, healthy, and sustainable work environment for all employees while delivering exceptional value to our customers and stakeholders.</p>
            </div>

            <div class="row gy-4">
                <div class="col-lg-6">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div class="info-content">
                                <p>মানুষের দৈনন্দিন প্রয়োজনীয় পণ্য ও সেবা এক প্ল্যাটফর্মে সরবরাহ করা</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-thermometer-sun"></i>
                            </div>
                            <div class="info-content">
                                <p>আন্তর্জাতিক মান বজায় রেখে উৎপাদন ও সেবা প্রদান করা</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="info-content">
                                <p>কর্মসংস্থান সৃষ্টি ও মানবসম্পদ উন্নয়নে অবদান রাখা</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-translate"></i>
                            </div>
                            <div class="info-content">
                                <p>গ্রাহকের সাথে দীর্ঘমেয়াদি বিশ্বাসভিত্তিক সম্পর্ক গড়ে তোলা</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-passport"></i>
                            </div>
                            <div class="info-content">
                                <p>সামাজিক দায়বদ্ধতা ও টেকসই উন্নয়নে সক্রিয় থাকা</p>
                            </div>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

@endsection