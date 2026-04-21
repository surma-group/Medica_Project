@extends('layouts.website')

@section('title', 'Contact - SURMA GROUP')

@section('content')

<!-- Page Header (NEW IMAGE) -->
<div class="container-fluid page-header py-5"
     style="background: url('{{ asset('website/img/cart-page-header-img.jpg') }}') center/cover no-repeat;">
    <h1 class="text-center text-white display-6">Contact</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Home</a>
        </li>
        <li class="breadcrumb-item active text-white">Contact</li>
    </ol>
</div>


<!-- Contact Section -->
<div class="container-fluid contact py-5">
    <div class="container py-5">
        <div class="p-5 bg-light rounded">

            <div class="row g-4">

                <!-- Title -->
                <div class="col-12 text-center">
                    <h1 class="text-primary">Get in touch</h1>
                    <p>আমাদের সাথে যোগাযোগ করুন। আমরা দ্রুত আপনাকে সহায়তা করব।</p>
                </div>

                <!-- OLD MAP (KEEP AS IT IS) -->
                <div class="col-lg-12">
                    <div class="rounded overflow-hidden">
                        <iframe class="w-100"
                            style="height: 400px;"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3652.451414143643!2d90.40959477439095!3d23.731276789512048!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8f623cf3611%3A0x1b15bfe121b648d7!2sResourceful%20Paltan%20City%2C%20Dhaka.!5e0!3m2!1sen!2sbd!4v1771137038249!5m2!1sen!2sbd"
                            loading="lazy">
                        </iframe>
                    </div>
                </div>

                <!-- FORM (NO STORE YET) -->
                <div class="col-lg-7">
                    <form action="#" method="POST">

                        <input type="text"
                               class="w-100 form-control border-0 py-3 mb-4"
                               placeholder="Your Name">

                        <input type="email"
                               class="w-100 form-control border-0 py-3 mb-4"
                               placeholder="Your Email">

                        <input type="text"
                               class="w-100 form-control border-0 py-3 mb-4"
                               placeholder="Subject">

                        <textarea class="w-100 form-control border-0 mb-4"
                                  rows="5"
                                  placeholder="Your Message"></textarea>

                        <button type="submit"
                                class="w-100 btn border-secondary py-3 bg-white text-primary">
                            Send Message
                        </button>

                    </form>
                </div>

                <!-- CONTACT INFO -->
                <div class="col-lg-5">

                    <div class="d-flex p-4 rounded mb-4 bg-white">
                        <i class="fas fa-map-marker-alt fa-2x text-primary me-4"></i>
                        <div>
                            <h4>Address</h4>
                            <p>Dhaka, Bangladesh</p>
                        </div>
                    </div>

                    <div class="d-flex p-4 rounded mb-4 bg-white">
                        <i class="fas fa-envelope fa-2x text-primary me-4"></i>
                        <div>
                            <h4>Email</h4>
                            <p>info@surmagroupind.com</p>
                        </div>
                    </div>

                    <div class="d-flex p-4 rounded bg-white">
                        <i class="fa fa-phone-alt fa-2x text-primary me-4"></i>
                        <div>
                            <h4>Phone</h4>
                            <p>+880 1312 468813</p>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

@endsection