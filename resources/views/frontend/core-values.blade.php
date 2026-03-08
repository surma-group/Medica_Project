@extends('layouts.website')

@section('title', 'Home - SURMA GROUP')

@section('content')

<!-- Page Title -->
<div class="page-title dark-background" style="background-image: url('{{ asset('website/assets/img/travel/showcase-12.webp') }}');">
    <div class="container position-relative">
        <h1>Core Values</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="current">Core Values</li>
            </ol>
        </nav>
    </div>
</div><!-- End Page Title -->

<section id="travel-destination-details" class="travel-destination-details section">

    <div class="container">
        <!-- Practical Information Section -->
        <div class="practical-info">
            <div class="section-header">
                <h2>আমাদের মূল মূল্যবোধ (Core Values)</h2>
                <p>আমাদের মূল মূল্যবোধ হলো: গ্রাহকের প্রতি সম্মান, সম্পর্কের প্রতি আগ্রহ, সময়ের প্রতি বিশেষ ধ্যান, এবং সামাজিক দায়বদ্ধতা।</p>
            </div>

            <div class="row gy-4">
                <div class="col-lg-6">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="info-content">
                                <p>সততা ও স্বচ্ছতা</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="info-content">
                                <p>গুণগত মানের প্রতি অঙ্গীকার</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-lightbulb"></i>
                            </div>
                            <div class="info-content">
                                <p>উদ্ভাবন ও উন্নয়ন</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="info-content">
                                <p>সামাজিক দায়বদ্ধতা</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-emoji-smile"></i>
                            </div>
                            <div class="info-content">
                                <p>গ্রাহক সন্তুষ্টি</p>
                            </div>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

@endsection