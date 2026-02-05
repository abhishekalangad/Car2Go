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

<div class="hero-section"
    style="height: 500px; display: flex; align-items: center; justify-content: center; background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('bg2.jpg'); background-size: cover; background-position: center;">
    <div class="container text-center text-white">
        <h1 class="display-3 font-weight-bold mb-4">Driving <span>Innovation</span></h1>
        <p class="lead max-width-700 mx-auto opacity-8">We are redefining the car rental and service industry through
            technology, transparency, and top-tier customer service.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-4">
            <h2 class="font-weight-bold mb-4">Our Mission</h2>
            <p class="lead text-muted">To provide a seamless, secure, and premium platform that connects vehicle owners,
                professional drivers, and service centers with users who value quality and reliability.</p>
            <p>Founded in 2023, CAR2GO has quickly become a trusted name in urban mobility. We don't just rent cars; we
                provide experiences. Whether you need a vehicle for a weekend getaway, a professional driver for a
                business trip, or expert maintenance for your own car, we've got you covered.</p>
            <div class="row mt-4 text-center">
                <div class="col-6 col-md-3 mb-3">
                    <div class="h3 font-weight-bold text-primary mb-0"><?php echo $total_cars; ?>+</div>
                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.6rem;">Verified
                        Cars</small>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="h3 font-weight-bold text-primary mb-0">
                        <?php echo number_format($total_users / 1000, 1); ?>k+</div>
                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.6rem;">Happy
                        Clients</small>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="h3 font-weight-bold text-primary mb-0"><?php echo $total_drivers; ?>+</div>
                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.6rem;">Expert
                        Drivers</small>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="h3 font-weight-bold text-primary mb-0"><?php echo $total_partners; ?>+</div>
                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.6rem;">Service
                        Hubs</small>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <img src="images/bg4.jpg" alt="About Car2Go" class="img-fluid rounded-lg shadow-2xl">
        </div>
    </div>

    <!-- Core Values -->
    <div class="row text-center mt-5">
        <div class="col-md-4 mb-4">
            <div class="p-4 bg-light rounded-lg h-100">
                <i class="fas fa-shield-alt fa-3x text-primary mb-4"></i>
                <h5 class="font-weight-bold">Safety First</h5>
                <p class="small text-muted">All our vehicles and partners undergo a rigorous 50-point verification check
                    before joining our platform.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="p-4 bg-light rounded-lg h-100">
                <i class="fas fa-gem fa-3x text-primary mb-4"></i>
                <h5 class="font-weight-bold">Premium Quality</h5>
                <p class="small text-muted">We curate only the best vehicles and professional partners to ensure a
                    top-tier experience every single time.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="p-4 bg-light rounded-lg h-100">
                <i class="fas fa-hand-holding-usd fa-3x text-primary mb-4"></i>
                <h5 class="font-weight-bold">Best Value</h5>
                <p class="small text-muted">Competitive pricing with no hidden costs. What you see is what you pay,
                    shared transparently from the start.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>