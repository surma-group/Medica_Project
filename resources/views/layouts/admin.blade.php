<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.admin.head')
</head>
<body>
<div class="loader"></div>
<div id="app">
    <div class="main-wrapper main-wrapper-1">

        @include('partials.admin.navbar')

        @include('partials.admin.sidebar')

        <div class="main-content">
            @yield('content')
        </div>

        @include('partials.admin.footer')

    </div>
</div>

</body>
</html>
