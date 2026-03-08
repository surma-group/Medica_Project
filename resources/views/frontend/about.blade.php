@extends('layouts.website')

@section('title', 'Home - SURMA GROUP')

@section('content')

<!-- Page Title -->
<div class="page-title dark-background" style="background-image: url('{{ asset('website/assets/img/travel/showcase-12.webp') }}');">
    <div class="container position-relative">
        <h1>About Us</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="current">About</li>
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
                        <h2>সুরমা গ্রুপ (SURMA GROUP)</h2>
                        <p>আমরা শুধু একটি ব্যবসায়িক প্রতিষ্ঠান নই, আমরা একটি স্বপ্নের নাম। একটি এমন স্বপ্ন, যেখানে সততা আমাদের ভিত্তি, গুণগত মান আমাদের পরিচয় এবং গ্রাহকের আস্থা আমাদের সবচেয়ে বড় সম্পদ। আমাদের গ্রুপ কোম্পানি মানুষের প্রয়োজনীয় বহুমুখী পণ্য ও সেবা নিয়ে একটি সমন্বিত প্ল্যাটফর্ম গড়ে তুলেছে—যার মূল লক্ষ্য শুধুমাত্র মুনাফা অর্জন নয়, বরং জাতীয় উন্নয়নে কার্যকর ভূমিকা রাখা। আমরা বিশ্বাস করি, একটি প্রতিষ্ঠান তখনই সত্যিকারের সফল হয়, যখন তার অগ্রগতি দেশের অগ্রগতির সাথে সামঞ্জস্যপূর্ণ হয়। বাংলাদেশ আজ সম্ভাবনার এক নতুন দিগন্তে দাঁড়িয়ে। তরুণ প্রজন্মের শক্তি, উদ্ভাবনী চিন্তা এবং উদ্যোক্তা মনোভাব আমাদের জাতির সবচেয়ে বড় সম্পদ। আমরা সেই শক্তিকে সম্মান করি, লালন করি এবং সামনে এগিয়ে যাওয়ার সাহস জোগাই।</p>
                        <p>We are not just a business organization; we are the name of a dream. A dream built on honesty as our foundation, quality as our identity, and the trust of our customers as our greatest asset. Our group of companies has created an integrated platform offering diverse essential products and services—not merely aiming for profit, but also playing an effective role in national development. We believe that an organization is truly successful only when its progress aligns with the progress of the country. Bangladesh today stands on the threshold of a new horizon of possibilities. The energy of the youth, innovative thinking, and entrepreneurial spirit are our nation’s greatest assets. We honor, nurture, and empower that strength to move forward with confidence.</p>
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