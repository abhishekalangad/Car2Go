<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Fetch Dynamic Stats for About Page
$total_cars = db_fetch_one($con, "SELECT COUNT(*) as count FROM rent")['count'] ?? 0;
$total_drivers = db_fetch_one($con, "SELECT COUNT(*) as count FROM driver_reg")['count'] ?? 0;
$total_partners = db_fetch_one($con, "SELECT COUNT(*) as count FROM service_reg")['count'] ?? 0;
$total_users = db_fetch_one($con, "SELECT COUNT(*) as count FROM login WHERE l_type = 'user'")['count'] ?? 0;

$page_title = 'About Us - CAR2GO';
include 'templates/header.php';
?>

<style>
    /* Premium Page Styles */
    .page-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 6rem 0 8rem;
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
        background: url('images/bg2.jpg') center/cover;
        opacity: 0.2;
        filter: blur(5px);
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .main-card {
        background: white;
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        margin-top: -80px;
        position: relative;
        z-index: 10;
        border: 1px solid #f1f5f9;
        margin-bottom: 4rem;
    }

    .stat-box {
        text-align: center;
        padding: 2rem;
        border-radius: 16px;
        background: #f8fafc;
        transition: transform 0.3s;
    }

    .stat-box:hover {
        transform: translateY(-5px);
        background: #eff6ff;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }

    .feature-icon-circle {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #2563eb;
        font-size: 2rem;
    }

    .team-card {
        border: none;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        height: 100%;
        transition: all 0.3s;
    }

    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .section-title h2 {
        font-weight: 800;
        margin-bottom: 1rem;
        color: #1e293b;
    }

    /* Mobile Optimization */
    @media (max-width: 768px) {
        .display-3 {
            font-size: 2.5rem;
        }

        .display-5 {
            font-size: 2rem;
        }

        .page-hero {
            padding: 4rem 0 6rem;
        }

        .main-card {
            padding: 2rem;
            margin-top: -60px;
        }

        .stat-box {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2rem;
        }

        .team-card {
            padding: 2rem !important;
        }
    }
</style>

<!-- Hero Section -->
<div class="page-hero">
    <div class="container hero-content">
        <h1 class="display-3 font-weight-bold mb-3 animate__animated animate__fadeInDown">Driving
            <span>Innovation</span>
        </h1>
        <p class="lead opacity-8 mx-auto animate__animated animate__fadeInUp" style="max-width: 700px;">
            Redefining the car rental and service industry through technology, transparency, and top-tier customer
            service.
        </p>
    </div>
</div>

<div class="container">
    <div class="main-card animate__animated animate__fadeInUp animate__delay-1s">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h6 class="text-primary font-weight-bold text-uppercase mb-2">Our Story</h6>
                <h2 class="font-weight-bold mb-4 display-5">More Than Just a <br>Rental Company</h2>
                <p class="text-muted leading-relaxed mb-4">
                    Founded in 2023, CAR2GO has quickly become a trusted name in urban mobility. We noticed a gap in the
                    market for a unified platform that not only provides premium vehicles but also connects users with
                    professional drivers and certified service centers.
                </p>
                <p class="text-muted leading-relaxed mb-4">
                    Our mission is simple: <strong>To provide a seamless, secure, and premium platform that connects
                        vehicle owners, professional drivers, and service centers with users who value quality and
                        reliability.</strong>
                </p>
                <div class="d-flex align-items-center mt-5">
                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=0D8ABC&color=fff"
                        class="rounded-circle mr-3" width="50" alt="CEO">
                    <div>
                        <div class="font-weight-bold">John Doe</div>
                        <div class="small text-muted">CEO & Founder</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 pl-lg-5">
                <div class="row">
                    <div class="col-6 mb-4">
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $total_cars; ?>+</div>
                            <div class="font-weight-bold text-dark">Premium Cars</div>
                        </div>
                    </div>
                    <div class="col-6 mb-4">
                        <div class="stat-box">
                            <div class="stat-number"><?php echo number_format($total_users / 1000, 1); ?>k</div>
                            <div class="font-weight-bold text-dark">Happy Users</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $total_drivers; ?>+</div>
                            <div class="font-weight-bold text-dark">Expert Drivers</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $total_partners; ?>+</div>
                            <div class="font-weight-bold text-dark">Service Hubs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Values Section -->
<div class="bg-light py-5 mb-5">
    <div class="container py-5">
        <div class="text-center mb-5 section-title">
            <h6 class="text-primary font-weight-bold text-uppercase">Why Choose Us</h6>
            <h2>Our Core Values</h2>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="team-card p-5 text-center">
                    <div class="feature-icon-circle">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="font-weight-bold mb-3">Safety First</h4>
                    <p class="text-muted mb-0">
                        Every vehicle and partner undergoes a rigorous 50-point verification check. Your safety is our
                        non-negotiable priority.
                    </p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="team-card p-5 text-center">
                    <div class="feature-icon-circle">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h4 class="font-weight-bold mb-3">Premium Quality</h4>
                    <p class="text-muted mb-0">
                        We curate only the best vehicles and professional partners to ensure a top-tier experience every
                        single time.
                    </p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="team-card p-5 text-center">
                    <div class="feature-icon-circle">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h4 class="font-weight-bold mb-3">Transparent Value</h4>
                    <p class="text-muted mb-0">
                        Competitive pricing with zero hidden costs. What you see is exactly what you pay, shared
                        securely from the start.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>