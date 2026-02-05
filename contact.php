<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

$page_title = 'Contact Us - CAR2GO';
include 'templates/header.php';
?>

<div class="hero-section"
    style="height: 400px; display: flex; align-items: center; justify-content: center; background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('bg4.jpg'); background-size: cover; background-position: center;">
    <div class="text-center text-white">
        <h1 class="display-3 font-weight-bold mb-3" style="letter-spacing: -2px;">Get In <span>Touch</span></h1>
        <p class="lead opacity-8">We're here to help you 24/7 with your car rental and service needs.</p>
    </div>
</div>

<div class="container py-5 mt-n5">
    <div class="row">
        <!-- Contact Info Cards -->
        <div class="col-lg-4 mb-4">
            <div class="glass-card p-4 h-100 text-center text-white border-0" style="background: var(--bg-dark);">
                <div class="premium-icon mb-4"><i class="fas fa-map-marker-alt fa-2x text-primary"></i></div>
                <h5 class="font-weight-bold mb-3">Our Location</h5>
                <p class="text-white-50">3481 Melrose Place, Beverly Hills,<br>New York City 90210, USA.</p>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="glass-card p-4 h-100 text-center text-white border-0" style="background: var(--bg-dark);">
                <div class="premium-icon mb-4"><i class="fas fa-phone fa-2x text-primary"></i></div>
                <h5 class="font-weight-bold mb-3">Call Us</h5>
                <p class="text-white-50">Main: +(000) 123 4565 32<br>Support: +(010) 123 4565 35</p>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="glass-card p-4 h-100 text-center text-white border-0" style="background: var(--bg-dark);">
                <div class="premium-icon mb-4"><i class="fas fa-envelope fa-2x text-primary"></i></div>
                <h5 class="font-weight-bold mb-3">Email Support</h5>
                <p class="text-white-50">info@car2go.com<br>support@car2go.com</p>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                <div class="card-body p-5">
                    <h3 class="font-weight-bold mb-4">Send a Message</h3>
                    <form action="#" method="post" class="premium-form text-dark">
                        <div class="row">
                            <div class="col-md-6 form-group mb-4">
                                <label class="small font-weight-bold text-muted">Full Name</label>
                                <input type="text" name="Name" class="form-control bg-light border-0"
                                    placeholder="Your name" required>
                            </div>
                            <div class="col-md-6 form-group mb-4">
                                <label class="small font-weight-bold text-muted">Email Address</label>
                                <input type="email" name="Email" class="form-control bg-light border-0"
                                    placeholder="Your email" required>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted">Subject</label>
                            <input type="text" name="Subject" class="form-control bg-light border-0"
                                placeholder="What can we help you with?" required>
                        </div>
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted">Message</label>
                            <textarea name="Message" class="form-control bg-light border-0" rows="5"
                                placeholder="Tell us more details..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-premium btn-gradient px-5 py-3">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-4">
            <div class="rounded-lg overflow-hidden shadow-lg h-100">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3539.812628729253!2d153.014155!3d-27.4750921!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6b915a0835840a2f%3A0xdd5e3f5c208dc0e1!2sMelbourne+St%2C+South+Brisbane+QLD+4101%2C+Australia!5e0!3m2!1sen!2sin!4v1492257477691"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>