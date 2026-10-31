<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.partner.head')
</head>
<body>
<div class="loader"></div>
<div id="app">
    <div class="main-wrapper main-wrapper-1">

        @include('partials.partner.navbar')

        @include('partials.partner.sidebar')

        <div class="main-content">
            @yield('content')
        </div>

        @include('partials.partner.footer')

    </div>
</div>

</body>
</html>
