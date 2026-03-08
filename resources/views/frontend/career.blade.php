@extends('layouts.website')

@section('title', 'Career - SURMA GROUP')

@section('content')

<!-- Page Title -->
<div class="page-title dark-background" style="background-image: url('{{ asset('website/assets/img/travel/showcase-12.webp') }}');">
    <div class="container position-relative">
        <h1>Career Opportunities</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="current">Career</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Career Section -->
<section class="career section">
    <div class="container">

        <div class="career-header text-center mb-4" style="background: linear-gradient(to bottom, #f9d8b1, #ffedda); padding: 15px; border-radius: 10px;">
            <h2 style="font-size: 28px; font-weight: bold;">জরুরী নিয়োগ বিজ্ঞপ্তি</h2>
        </div>

        <p class="text-center mb-4">
            সুরমা গ্রুপ একটি মাল্টিমিডিয়াম মার্কেটিং ও অ্যাডভার্টাইজিং এবং পাইকারি সরবরাহকারী কোম্পানি। উক্ত কোম্পানির কিছু পদে জনবল নিয়োগের জন্য নিম্নবর্ণিত পদে স্বল্পসংখ্যক জনবল প্রয়োজন। বিস্তারিত নিচের টেবিল অনুযায়ী।
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-primary">
                    <tr>
                        <th>পদ নং</th>
                        <th>পদবি</th>
                        <th>পদের সংখ্যা</th>
                        <th>শিক্ষাগত যোগ্যতা</th>
                        <th>অভিজ্ঞতা</th>
                        <th>বেতন</th>
                        <th>কর্মস্থল</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>জেনারেল ম্যানেজার (GM)</td>
                        <td>০১ জন</td>
                        <td>স্নাতকোত্তর</td>
                        <td>৫ বছর</td>
                        <td>আলোচনা সাপেক্ষে</td>
                        <td>ঢাকা</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>অ্যাসিস্ট্যান্ট জেনারেল ম্যানেজার (AGM)</td>
                        <td>০২ জন</td>
                        <td>স্নাতকোত্তর</td>
                        <td>3 বছর</td>
                        <td>আলোচনা সাপেক্ষে</td>
                        <td>ঢাকা</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>এরিয়া ম্যানেজার (এমএম)</td>
                        <td>8 জন</td>
                        <td>স্নাতক</td>
                        <td>3 বছর</td>
                        <td>আলোচনা সাপেক্ষে</td>
                        <td>সমস্ত বাংলাদেশ</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>ব্রাঞ্চ ম্যানেজার/সেলস রিপ্রেজেন্টেটিভ/ডেলিভারি ম্যান</td>
                        <td>প্রয়োজনীয় সংখ্যক</td>
                        <td>সিথিলযোগ্য</td>
                        <td>২ বছর</td>
                        <td>আলোচনা সাপেক্ষে</td>
                        <td>সমস্ত বাংলাদেশ</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>অ্যাসিস্ট্যান্ট ম্যানেজার, অফিস পিয়ন, নিরাপত্তা প্রহরী, অ্যাকাউন্টিং অফিসার</td>
                        <td>প্রত্যেকের জন্য ০১ জন</td>
                        <td>সিথিলযোগ্য</td>
                        <td>সিথিলযোগ্য</td>
                        <td>আলোচনা সাপেক্ষে</td>
                        <td>সমস্ত বাংলাদেশ</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>এক্সিকিউটিভ অফিসার</td>
                        <td>০১ জন</td>
                        <td>স্নাতক</td>
                        <td>সিথিলযোগ্য</td>
                        <td>আলোচনা সাপেক্ষে</td>
                        <td>সমস্ত বাংলাদেশ</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="mt-3">
            আবেদন প্রার্থীদের অফিসে উপস্থিতির সময় (৯.০০-৫.০০ বজি) যথাযথ ব্যবস্থাপনা, গ্রহিত আবেদন/সিভি জমা দিতে হবে। সুরমা গ্রুপ-এর অফিসিয়াল বিজ্ঞপ্তি অনুযায়ী সকল পদে আবেদনকারীদের প্রার্থিতা যাচাই করা হবে।
        </p>

    </div>
</section>

@endsection