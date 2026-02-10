<?php
/**
 * CAR2GO - Premium Landing Page
 */

require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Fetch Dynamic Stats
$total_cars = db_fetch_one($con, "SELECT COUNT(*) as count FROM rent")['count'] ?? 0;
$total_drivers = db_fetch_one($con, "SELECT COUNT(*) as count FROM driver_reg")['count'] ?? 0;
$total_partners = db_fetch_one($con, "SELECT COUNT(*) as count FROM service_reg")['count'] ?? 0;
$total_users = db_fetch_one($con, "SELECT COUNT(*) as count FROM login WHERE l_type = 'user'")['count'] ?? 0;

// Fetch Featured Cars (Random 3 Approved)
$featured_cars_query = "SELECT * FROM rent WHERE r_status = 'approve' ORDER BY RAND() LIMIT 3";
$featured_cars = db_fetch_all($con, $featured_cars_query);

$no_padding = true;
$page_title = 'Welcome to CAR2GO - Premium Car Rental & Driver Services';
require_once 'templates/header.php';
?>

<style>
   /* Custom Flex Utilities for BS3 */
   .d-flex {
      display: -webkit-box;
      display: -webkit-flex;
      display: -ms-flexbox;
      display: flex;
   }

   .align-items-center {
      -webkit-box-align: center;
      -webkit-align-items: center;
      -ms-flex-align: center;
      align-items: center;
   }

   .justify-content-center {
      -webkit-box-pack: center;
      -webkit-justify-content: center;
      -ms-flex-pack: center;
      justify-content: center;
   }

   .h-100 {
      height: 100% !important;
   }

   /* Carousel Enhancement */
   .item {
      height: 100vh;
      min-height: 600px;
      position: relative;
      background-size: cover;
      background-position: center;
      width: 100%;
      /* Ensure width */
   }

   /* Force Bootrap 3 Behavior if CSS is missing */
   .carousel-inner>.item {
      display: none;
      position: relative;
      -webkit-transition: .6s ease-in-out left;
      -o-transition: .6s ease-in-out left;
      transition: .6s ease-in-out left;
   }

   .carousel-inner>.item>img,
   .carousel-inner>.item>a>img {
      line-height: 1;
   }

   .carousel-inner>.active,
   .carousel-inner>.next,
   .carousel-inner>.prev {
      display: block;
   }

   .carousel-inner>.active {
      left: 0;
   }

   .carousel-inner>.next,
   .carousel-inner>.prev {
      position: absolute;
      top: 0;
      width: 100%;
   }

   .carousel-inner>.next {
      left: 100%;
   }

   .carousel-inner>.prev {
      left: -100%;
   }

   .carousel-inner>.next.left,
   .carousel-inner>.prev.right {
      left: 0;
   }

   .carousel-inner>.active.left {
      left: -100%;
   }

   .carousel-inner>.active.right {
      left: 100%;
   }

   .item::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, rgba(15, 23, 42, 0.7) 0%, rgba(15, 23, 42, 0.3) 50%, rgba(15, 23, 42, 0.8) 100%);
      z-index: 1;
   }

   .hero-content {
      position: relative;
      z-index: 2;
      max-width: 900px;
      text-align: center;
      padding: 0 2rem;
      margin-top: 50px;
      /* Safe top margin */
   }

   .hero-title {
      font-size: 4.5rem;
      font-weight: 800;
      margin-bottom: 1.5rem;
      letter-spacing: -2.5px;
      line-height: 1.05;
      color: white;
      /* Fallback */
      text-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
   }

   .hero-title span {
      display: block;
      color: #3b82f6;
      /* Fallback */
      background: -webkit-linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      filter: drop-shadow(0 5px 15px rgba(37, 99, 235, 0.4));
   }

   .hero-subtitle {
      font-size: 1.25rem;
      margin-bottom: 2.5rem;
      opacity: 0.85;
      font-weight: 400;
      max-width: 650px;
      margin-left: auto;
      margin-right: auto;
      line-height: 1.6;
   }

   .glass-card {
      background: rgba(15, 23, 42, 0.6);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 3rem;
      box-shadow: var(--glass-shadow);
   }

   /* Action Buttons */
   .btn-premium {
      padding: 1rem 2.5rem;
      font-weight: 600;
      border-radius: 50px;
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      text-transform: uppercase;
      letter-spacing: 1px;
      display: inline-block;
      margin: 0 10px;
   }

   .btn-gradient {
      background: #2563eb;
      background: linear-gradient(135deg, #2563eb, #3b82f6);
      color: white !important;
      border: none;
   }

   .btn-gradient:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(37, 99, 235, 0.4);
      color: white;
   }

   .btn-outline-glass {
      border: 2px solid white;
      color: white !important;
      background: transparent;
   }

   .btn-outline-glass:hover {
      background: white;
      color: #0f172a !important;
      transform: translateY(-5px);
   }

   /* Carousel Controls and Indicators */
   .carousel-indicators li {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      margin: 0 8px;
      background-color: rgba(255, 255, 255, 0.3);
      border: none;
   }

   .carousel-indicators li.active {
      background-color: #2563eb;
      transform: scale(1.2);
   }

   .stats-floating-overlay {
      position: absolute;
      bottom: 50px;
      left: 0;
      right: 0;
      z-index: 10;
   }

   .stats-floating-overlay .glass-card {
      padding: 1.5rem 2rem !important;
      background: rgba(15, 23, 42, 0.6);
      width: auto !important;
      min-width: 600px;
   }

   .tracking-widest {
      letter-spacing: 0.15em;
   }

   .extra-small {
      font-size: 0.65rem;
   }

   /* Services Section */
   .services-container {
      padding: 6rem 0;
      background: #f8fafc;
   }

   .section-title {
      text-align: center;
      margin-bottom: 4rem;
   }

   .section-title h2 {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--bg-dark);
   }

   .section-title .divider {
      width: 60px;
      height: 4px;
      background: var(--primary-color);
      margin: 1.5rem auto;
   }

   .service-card {
      background: white;
      padding: 3rem;
      border-radius: 24px;
      text-align: center;
      transition: all 0.4s ease;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      border: 1px solid #f1f5f9;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
   }

   .service-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      border-color: #2563eb;
   }

   .service-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 2rem;
      background: #eff6ff;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: var(--primary-color);
      transition: all 0.3s ease;
   }

   .service-card:hover .service-icon {
      background: var(--primary-color);
      color: white;
      transform: rotateY(180deg);
   }

   .features-section {
      background: var(--bg-dark);
      color: white;
      padding: 6rem 0;
      position: relative;
   }

   .feature-item {
      display: flex;
      align-items: center;
      margin-bottom: 2rem;
   }

   .feature-check {
      width: 32px;
      height: 32px;
      background: var(--primary-color);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1.5rem;
      font-size: 0.8rem;
   }

   /* Mobile Optimization */
   @media (max-width: 768px) {
      .hero-title {
         font-size: 2.5rem !important;
         letter-spacing: -1px;
         line-height: 1.2;
      }

      .hero-subtitle {
         font-size: 1rem !important;
         padding: 0 10px;
         margin-bottom: 1.5rem;
      }

      .btn-premium {
         padding: 0.8rem 2rem;
         font-size: 0.9rem;
         margin: 5px;
         display: block;
         width: 100%;
      }

      .section-title h2 {
         font-size: 2rem;
      }

      .service-card {
         padding: 2rem;
         margin-bottom: 1.5rem;
      }

      .feature-card {
         min-height: auto;
         margin-bottom: 1.5rem;
      }
   }
</style>

<!-- Hero Carousel -->
<div id="heroCarousel" class="carousel slide" data-ride="carousel">
   <!-- Indicators -->
   <ol class="carousel-indicators">
      <li data-target="#heroCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#heroCarousel" data-slide-to="1"></li>
      <li data-target="#heroCarousel" data-slide-to="2"></li>
   </ol>

   <div class="carousel-inner" role="listbox">
      <!-- Slide 1 -->
      <div class="item active" style="background-image: url('images/bg3.jpg');">
         <div class="container d-flex align-items-center justify-content-center h-100 text-center">
            <div class="hero-content">
               <h1 class="hero-title animate__animated animate__fadeInUp">Experience the Journey,
                  <br><span>Redefined</span>
               </h1>
               <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">Premium Car Rentals for
                  every destination. Luxury, comfort, and reliability at your fingertips.</p>
               <div class="animate__animated animate__fadeInUp animate__delay-2s" style="margin-top: 2rem;">
                  <a href="viewcars.php" class="btn btn-premium btn-gradient">Explore Fleet</a>
                  <a href="login.php" class="btn btn-premium btn-outline-glass">Get Started</a>
               </div>
            </div>
         </div>
      </div>

      <!-- Slide 2 -->
      <div class="item" style="background-image: url('images/bg7.jpg');">
         <div class="container d-flex align-items-center justify-content-center h-100 text-center">
            <div class="hero-content">
               <h1 class="hero-title animate__animated animate__fadeInUp">Elite Chauffeur <br><span>Services</span></h1>
               <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">Professional, vetted, and
                  experienced drivers for your personal or commercial
                  vehicle. Your safety is our priority.</p>
               <div class="mt-4 animate__animated animate__fadeInUp animate__delay-2s" style="margin-top: 2rem;">
                  <a href="viewdriv.php" class="btn btn-premium btn-gradient">Hire a Driver</a>
               </div>
            </div>
         </div>
      </div>

      <!-- Slide 3 -->
      <div class="item" style="background-image: url('images/bg8.jpg');">
         <div class="container d-flex align-items-center justify-content-center h-100 text-center">
            <div class="hero-content">
               <h1 class="hero-title animate__animated animate__fadeInUp">Global Service <br><span>Network</span></h1>
               <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">Expert vehicle maintenance
                  and diagnostics from our trusted partner centers.
                  Keep your car in peak condition.</p>
               <div class="mt-4 animate__animated animate__fadeInUp animate__delay-2s" style="margin-top: 2rem;">
                  <a href="viewservicee1.php" class="btn btn-premium btn-gradient">Find a Center</a>
               </div>
            </div>
         </div>
      </div>
   </div>


   <!-- Controls -->
   <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
   </a>
   <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
   </a>
</div>

<!-- Stats Counter Section -->
<section class="stats-section" style="padding: 40px 0; background: #fff; border-bottom: 1px solid #eef2f6;">
   <div class="container">
      <div class="row text-center">
         <div class="col-md-3 col-xs-6 mb-4 mb-md-0">
            <div class="stat-item" style="padding: 20px;">
               <div class="h2 font-weight-bold mb-1" style="color: #2563eb; margin: 0;">
                  <?php echo number_format($total_cars); ?>+
               </div>
               <div class="text-muted small text-uppercase tracking-widest">Vehicles</div>
            </div>
         </div>
         <div class="col-md-3 col-xs-6 mb-4 mb-md-0">
            <div class="stat-item" style="padding: 20px;">
               <div class="h2 font-weight-bold mb-1" style="color: #2563eb; margin: 0;">
                  <?php echo number_format($total_drivers); ?>+
               </div>
               <div class="text-muted small text-uppercase tracking-widest">Elite Drivers</div>
            </div>
         </div>
         <div class="col-md-3 col-xs-6 mb-4 mb-md-0">
            <div class="stat-item" style="padding: 20px;">
               <div class="h2 font-weight-bold mb-1" style="color: #2563eb; margin: 0;">
                  <?php echo number_format($total_partners); ?>+
               </div>
               <div class="text-muted small text-uppercase tracking-widest">Service Centers</div>
            </div>
         </div>
         <div class="col-md-3 col-xs-6 mb-4 mb-md-0">
            <div class="stat-item" style="padding: 20px;">
               <div class="h2 font-weight-bold mb-1" style="color: #2563eb; margin: 0;">
                  <?php echo number_format($total_users); ?>+
               </div>
               <div class="text-muted small text-uppercase tracking-widest">Happy Clients</div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Services Section -->
<section class="services-container">
   <div class="container">
      <div class="section-title">
         <h2>Our Premium Services</h2>
         <div class="divider"></div>
         <p class="text-muted">We provide a wide range of logistics and transport solutions tailored to your needs.</p>
      </div>

      <div class="row">
         <div class="col-lg-4 col-md-6 mb-4">
            <div class="service-card">
               <div class="service-icon">
                  <i class="fas fa-taxi"></i>
               </div>
               <h3>Car Rental</h3>
               <p class="text-muted">Wide selection of luxury and economy cars for your journeys. Self-drive or with a
                  chauffeur.</p>
               <a href="viewcars.php" class="text-primary font-weight-bold mt-3 d-inline-block">View Collection <i
                     class="fas fa-arrow-right ml-1"></i></a>
            </div>
         </div>

         <div class="col-lg-4 col-md-6 mb-4">
            <div class="service-card">
               <div class="service-icon">
                  <i class="fas fa-user-tie"></i>
               </div>
               <h3>Driver Booking</h3>
               <p class="text-muted">Professional, vetted, and experienced drivers for your personal or commercial
                  vehicle.</p>
               <a href="viewdriv.php" class="text-primary font-weight-bold mt-3 d-inline-block">Book Now <i
                     class="fas fa-arrow-right ml-1"></i></a>
            </div>
         </div>

         <div class="col-lg-4 col-md-6 mb-4">
            <div class="service-card">
               <div class="service-icon">
                  <i class="fas fa-tools"></i>
               </div>
               <h3>Service Center</h3>
               <p class="text-muted">Expert vehicle maintenance, repairs, and diagnostics from our trusted partner
                  centers.</p>
               <a href="viewservicee1.php" class="text-primary font-weight-bold mt-3 d-inline-block">Find Centers <i
                     class="fas fa-arrow-right ml-1"></i></a>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Featured Vehicles Section (Dynamic) -->
<?php if (!empty($featured_cars)): ?>
   <section class="featured-section" style="padding: 80px 0; background: #f8fafc;">
      <div class="container">
         <div class="text-center mb-5 section-title">
            <h2 class="font-weight-bold" style="color: #0f172a;">Featured Vehicles</h2>
            <div class="divider"></div>
            <p class="text-muted">Handpicked premium cars available for your next journey.</p>
         </div>

         <div class="row">
            <?php foreach ($featured_cars as $car): ?>
               <div class="col-md-4 col-sm-6 mb-4">
                  <div class="card border-0 shadow-sm h-100"
                     style="border-radius: 16px; overflow: hidden; transition: transform 0.3s; background: white; margin-bottom: 30px;">
                     <div style="height: 200px; background: #f1f5f9; overflow: hidden; position: relative;">
                        <img src="uploads/cars/<?php echo htmlspecialchars($car['r_car']); ?>"
                           alt="<?php echo htmlspecialchars($car['r_mname']); ?>"
                           style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="position-absolute"
                           style="top: 15px; right: 15px; background: rgba(0,0,0,0.6); color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem;">
                           <?php echo htmlspecialchars($car['r_year']); ?>
                        </div>
                     </div>
                     <div class="card-body p-4"
                        style="border: 1px solid #e2e8f0; border-top: none; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                        <h4 class="font-weight-bold mb-1" style="color: #0f172a; margin-top: 0;">
                           <?php echo htmlspecialchars($car['r_company'] . ' ' . $car['r_mname']); ?>
                        </h4>
                        <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt mr-1"></i>
                           <?php echo htmlspecialchars($car['r_pincode']); ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3"
                           style="display: flex; justify-content: space-between; align-items: center;">
                           <span class="text-primary font-weight-bold"
                              style="font-size: 1.2rem;">₹<?php echo number_format($car['rent_amt']); ?><small
                                 class="text-muted">/day</small></span>
                           <a href="viewcars.php" class="btn btn-sm btn-outline-primary rounded-pill px-4"
                              style="border-radius: 50px; border: 1px solid #3b82f6; padding: 8px 20px;">Book Now</a>
                        </div>
                     </div>
                  </div>
               </div>
            <?php endforeach; ?>
         </div>

         <div class="text-center mt-5">
            <a href="viewcars.php" class="btn btn-premium btn-gradient">View All Cars</a>
         </div>
      </div>
   </section>
<?php endif; ?>

<!-- Features Section -->
<section class="features-section" style="background: #fff; padding: 80px 0;">
   <div class="container">
      <div class="text-center mb-5">
         <h2 class="font-weight-bold" style="color: #0f172a; font-size: 2.5rem;">Why Choose CAR2GO?</h2>
         <p class="text-muted" style="max-width: 600px; margin: 0 auto; font-size: 1.1rem;">We prioritize your safety,
            comfort, and time. Experience the difference with our premium fleet and professional service.</p>
         <div style="width: 60px; height: 4px; background: #2563eb; margin: 20px auto;"></div>
      </div>

      <div class="row">
         <!-- Feature 1 -->
         <div class="col-md-4 mb-4">
            <div class="feature-card h-100">
               <div class="icon-wrapper mb-4">
                  <i class="fas fa-user-shield"></i>
               </div>
               <h4 class="font-weight-bold mb-3" style="color: #0f172a;">Trusted Drivers</h4>
               <p class="text-muted">Every driver undergoes rigorous background checks, professional training, and
                  regular evaluations to ensure your safety.</p>
            </div>
         </div>

         <!-- Feature 2 -->
         <div class="col-md-4 mb-4">
            <div class="feature-card h-100">
               <div class="icon-wrapper mb-4">
                  <i class="fas fa-headset"></i>
               </div>
               <h4 class="font-weight-bold mb-3" style="color: #0f172a;">24/7 Support</h4>
               <p class="text-muted">Our dedicated support team is available round the clock to assist you with
                  bookings, queries, and emergency roadside assistance.</p>
            </div>
         </div>

         <!-- Feature 3 -->
         <div class="col-md-4 mb-4">
            <div class="feature-card h-100">
               <div class="icon-wrapper mb-4">
                  <i class="fas fa-gem"></i>
               </div>
               <h4 class="font-weight-bold mb-3" style="color: #0f172a;">Premium Fleet</h4>
               <p class="text-muted">Choose from our exclusive collection of high-end vehicles, regularly maintained and
                  detailed for a pristine experience.</p>
            </div>
         </div>
      </div>
   </div>
</section>

<style>
   /* Feature Cards */
   .feature-card {
      background: #f8fafc;
      padding: 40px 30px;
      border-radius: 20px;
      transition: all 0.3s ease;
      border: 1px solid #e2e8f0;
      text-align: center;
      min-height: 300px;
      /* Ensure uniform height */
   }

   .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
      background: #fff;
      border-color: #2563eb;
   }

   .icon-wrapper {
      width: 70px;
      height: 70px;
      background: #eff6ff;
      color: #2563eb;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      margin: 0 auto;
      transition: all 0.3s ease;
   }

   .feature-card:hover .icon-wrapper {
      background: #2563eb;
      color: #fff;
      transform: scale(1.1);
   }
</style>

<script>
   $(document).ready(function () {
      $('#heroCarousel').carousel({
         interval: 5000,
         pause: "hover"
      });
   });
</script>

<?php require_once 'templates/footer.php'; ?>