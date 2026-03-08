<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Ogani Template">
    <meta name="keywords" content="Ogani, unica, creative">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Medica')</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ asset('website/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/elegant-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/slicknav.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/assets/css/style.css') }}">

    @stack('styles')
</head>

<body>

    {{-- Preloader --}}
    <div id="preloder">
        <div class="loader"></div>
    </div>

    {{-- Header --}}
    @include('partials.website.header')

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.website.footer')

    <!-- Js Plugins -->
    <script src="{{ asset('website/assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('website/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('website/assets/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('website/assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('website/assets/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('website/assets/js/mixitup.min.js') }}"></script>
    <script src="{{ asset('website/assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('website/assets/js/main.js') }}"></script>

    @stack('scripts')

</body>
</html>