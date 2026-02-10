<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

$page_title = 'Contact Us - CAR2GO';
include 'templates/header.php';
?>

<style>
    /* Premium Page Styles */
    .page-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 6rem 0 10rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .page-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('images/bg4.jpg') center/cover;
        opacity: 0.2;
        filter: blur(5px);
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .contact-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        margin-top: -100px;
        position: relative;
        z-index: 10;
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .contact-info-panel {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        height: 100%;
        padding: 3rem;
        position: relative;
        overflow: hidden;
    }

    .contact-info-panel::after {
        content: '';
        position: absolute;
        bottom: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 2.5rem;
    }

    .info-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-right: 1.5rem;
        flex-shrink: 0;
    }

    .form-control-lg {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 1.5rem 1rem;
        font-size: 1rem;
        background: #f8fafc;
    }

    .form-control-lg:focus {
        background: white;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .social-links a {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 50%;
        margin-right: 10px;
        transition: all 0.3s;
        text-decoration: none;
    }

    .social-links a:hover {
        background: white;
        color: #2563eb;
        transform: translateY(-3px);
    }

    /* Mobile Optimization */
    @media (max-width: 768px) {
        .display-3 {
            font-size: 2.5rem;
        }

        .page-hero {
            padding: 4rem 0 6rem;
        }

        .contact-card {
            margin-top: -40px;
        }

        .p-5 {
            padding: 2rem !important;
        }

        .contact-info-panel {
            padding: 2rem !important;
        }

        .contact-info-panel::after {
            width: 150px;
            height: 150px;
        }
    }
</style>

<!-- Hero Section -->
<div class="page-hero">
    <div class="container hero-content">
        <h1 class="display-3 font-weight-bold mb-3 animate__animated animate__fadeInDown">Get in <span>Touch</span></h1>
        <p class="lead opacity-8 mx-auto animate__animated animate__fadeInUp" style="max-width: 600px;">
            Have questions or need assistance? Our support team is here to help you 24/7.
        </p>
    </div>
</div>

<div class="container mb-5">
    <div class="contact-card animate__animated animate__fadeInUp animate__delay-1s">
        <div class="row no-gutters">
            <!-- Left Panel: Contact Info -->
            <div class="col-lg-5">
                <div class="contact-info-panel">
                    <h3 class="font-weight-bold mb-4">Contact Information</h3>
                    <p class="mb-5 opacity-8">Fill up the form and our Team will get back to you within 24 hours.</p>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Call Us</h6>
                            <p class="mb-0 opacity-8">+91 98765 43210</p>
                            <p class="mb-0 opacity-8">+91 12345 67890</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Email Us</h6>
                            <p class="mb-0 opacity-8">support@car2go.com</p>
                            <p class="mb-0 opacity-8">info@car2go.com</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Visit Us</h6>
                            <p class="mb-0 opacity-8">
                                123 Premium Tower, Tech Park,<br>
                                Bangalore, Karnataka - 560001
                            </p>
                        </div>
                    </div>

                    <div class="mt-auto pt-5">
                        <h6 class="font-weight-bold mb-3">Follow Us</h6>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Form -->
            <div class="col-lg-7">
                <div class="p-5">
                    <h3 class="font-weight-bold text-dark mb-4">Send us a Message</h3>

                    <form action="#" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="small font-weight-bold text-muted ml-1">Your Name</label>
                                <input type="text" name="Name" class="form-control form-control-lg"
                                    placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="small font-weight-bold text-muted ml-1">Email Address</label>
                                <input type="email" name="Email" class="form-control form-control-lg"
                                    placeholder="john@example.com" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="small font-weight-bold text-muted ml-1">Subject</label>
                            <input type="text" name="Subject" class="form-control form-control-lg"
                                placeholder="How can we help?" required>
                        </div>

                        <div class="mb-4">
                            <label class="small font-weight-bold text-muted ml-1">Message</label>
                            <textarea name="Message" class="form-control form-control-lg" rows="4"
                                placeholder="Write your message here..." required></textarea>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-lg px-5 font-weight-bold shadow-sm"
                                style="background: linear-gradient(135deg, #3b82f6, #2563eb); border:none; border-radius: 50px;">
                                SEND MESSAGE <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid p-0 mb-n5">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3888.003673033878!2d77.59456267491753!3d12.97159868734368!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae1677c7545469%3A0xe4a275f46408285e!2sUB%20City!5e0!3m2!1sen!2sin!4v1689254321098!5m2!1sen!2sin"
        width="100%" height="450" style="border:0; filter: grayscale(100%);" allowfullscreen="" loading="lazy">
    </iframe>
</div>

<?php include 'templates/footer.php'; ?>