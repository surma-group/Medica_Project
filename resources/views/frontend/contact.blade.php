@extends('layouts.website')

@section('title', 'Home - SURMA GROUP')

@section('content')

<!-- Page Title -->
<div class="page-title dark-background" style="background-image: url('{{ asset('website/assets/img/travel/showcase-12.webp') }}');">
    <div class="container position-relative">
        <h1>Contact</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="current">Contact</li>
            </ol>
        </nav>
    </div>
</div><!-- End Page Title -->

<!-- Contact Section -->
<section id="contact" class="contact section">

    <div class="container">
        <div class="contact-wrapper">
            <div class="contact-info-panel">
                <div class="contact-info-header">
                    <h3>Contact Information</h3>
                    <p>আমাদের সাথে যোগাযোগ করুন এবং আপনার ব্যবসায়িক প্রয়োজনীয়তা সম্পর্কে আমাদের জানান। আমরা আপনার সাফল্যের অংশীদার হতে আগ্রহী।</p>
                </div>

                <div class="contact-info-cards">
                    <div class="info-card">
                        <div class="icon-container">
                            <i class="bi bi-pin-map-fill"></i>
                        </div>
                        <div class="card-content">
                            <h4>Our Location</h4>
                            <p>Level 8/Room No. 804, 51/51-A, Resourcful Paltan City, Purana Paltan Line, Dhaka-1000</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="icon-container">
                            <i class="bi bi-envelope-open"></i>
                        </div>
                        <div class="card-content">
                            <h4>Email Us</h4>
                            <p>info@surmagroupind.com</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="icon-container">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div class="card-content">
                            <h4>Call Us</h4>
                            <p>+880 1312 468813</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="icon-container">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="card-content">
                            <h4>Working Hours</h4>
                            <p>Saturday-Thursday: 10AM - 7PM</p>
                        </div>
                    </div>
                </div>

                <div class="social-links-panel">
                    <h5>Follow Us</h5>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <div class="contact-form-panel">
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3652.451414143643!2d90.40959477439095!3d23.731276789512048!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8f623cf3611%3A0x1b15bfe121b648d7!2sResourceful%20Paltan%20City%2C%20Dhaka.!5e0!3m2!1sen!2sbd!4v1771137038249!5m2!1sen!2sbd" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <div class="form-container">
                    <h3>Send Us a Message</h3>
                    <p>আপনার প্রয়োজনীয়তা সম্পর্কে আমাদের জানান।</p>

                    <form action="forms/contact.php" method="post" class="php-email-form">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="nameInput" name="name" placeholder="Full Name" required="">
                            <label for="nameInput">Full Name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="emailInput" name="email" placeholder="Email Address" required="">
                            <label for="emailInput">Email Address</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="subjectInput" name="subject" placeholder="Subject" required="">
                            <label for="subjectInput">Subject</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="messageInput" name="message" rows="5" placeholder="Your Message" style="height: 150px" required=""></textarea>
                            <label for="messageInput">Your Message</label>
                        </div>

                        <div class="my-3">
                            <div class="loading">Loading</div>
                            <div class="error-message"></div>
                            <div class="sent-message">Your message has been sent. Thank you!</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn-submit">Send Message <i class="bi bi-send-fill ms-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section><!-- /Contact Section -->

@endsection