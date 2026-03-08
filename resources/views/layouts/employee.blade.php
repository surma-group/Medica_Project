<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.employee.head')
</head>
<body>
<div class="loader"></div>
<div id="app">
    <div class="main-wrapper main-wrapper-1">

        @include('partials.employee.navbar')

        @include('partials.employee.sidebar')

        <div class="main-content">
            @yield('content')
        </div>

        @include('partials.employee.footer')

    </div>
</div>

</body>
</html>
