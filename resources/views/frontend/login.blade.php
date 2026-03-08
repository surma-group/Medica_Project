<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Employees Login</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/bundles/bootstrap-social/bootstrap-social.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/components.css') }}">

    <!-- Custom style CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/custom.css') }}">
    <link rel='shortcut icon' type='image/x-icon' href='{{ asset('admin/assets/img/favicon.ico') }}' />
    <style>
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 5;
        }

        .password-toggle:hover {
            color: #000;
        }
    </style>

</head>

<body>
    <div class="loader"></div>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div
                        class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <img src="{{ asset('admin/assets/img/logo.png') }}" alt="Logo" class="img-fluid"
                                style="max-height: 100px;">
                        </div>
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Login</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('login.perform') }}" class="needs-validation"
                                    novalidate="">
                                    @csrf
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            tabindex="1" required autofocus value="{{ old('email') }}">
                                        @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @else
                                        <div class="invalid-feedback">
                                            Please fill in your email
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="position-relative">
                                            <input id="password" type="password"
                                                class="form-control pr-5 @error('password') is-invalid @enderror"
                                                name="password" tabindex="2" required>

                                            <span class="password-toggle" onclick="togglePassword()">
                                                <i id="toggleIcon" class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @else
                                        <div class="invalid-feedback">
                                            Please fill in your password
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="remember" class="custom-control-input"
                                                tabindex="3" id="remember-me" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="remember-me">Remember Me</label>
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
                                            Login
                                        </button>
                                    </div>

                                    @if ($errors->any())
                                    <div class="text-danger text-center mt-2">
                                        {{ $errors->first() }}
                                    </div>
                                    @endif

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- JS Files -->
    <script src="{{ asset('admin/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/scripts.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom.js') }}"></script>

    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const icon = document.getElementById("toggleIcon");

            if (password.type === "password") {
                password.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                password.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>

</html>