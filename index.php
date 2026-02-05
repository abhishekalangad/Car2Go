<?php
/**
 * CAR2GO - Premium Landing Page
 */

$page_title = 'Welcome to CAR2GO - Premium Car Rental & Driver Services';
require_once 'templates/header.php';
?>

<style>
   /* Hero Section */
   .hero-section {
      position: relative;
      height: 100vh;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.7)), url('images/bg3.jpg');
      background-size: cover;
      background-position: center;
      color: white;
      overflow: hidden;
   }

   .hero-content {
      max-width: 800px;
      text-align: center;
      padding: 2rem;
      z-index: 2;
      animation: fadeInUp 1s ease-out;
   }

   .hero-title {
      font-size: 4rem;
      font-weight: 700;
      margin-bottom: 1rem;
      letter-spacing: -2px;
      line-height: 1.1;
   }

   .hero-subtitle {
      font-size: 1.5rem;
      margin-bottom: 2rem;
      opacity: 0.9;
      font-weight: 300;
   }

   .glass-card {
      background: var(--glass-bg);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid var(--glass-border);
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
   }

   .btn-gradient {
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      color: white;
      border: none;
   }

   .btn-gradient:hover {
      transform: translateY(-5px) scale(1.05);
      box-shadow: 0 10px 20px rgba(37, 99, 235, 0.4);
      color: white;
   }

   .btn-outline-glass {
      border: 1px solid white;
      color: white;
      background: transparent;
   }

   .btn-outline-glass:hover {
      background: white;
      color: var(--bg-dark);
      transform: translateY(-5px);
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
      border-color: var(--accent-color);
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

   .service-card h3 {
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--bg-dark);
   }

   /* Floating Animations */
   @keyframes fadeInUp {
      from {
         opacity: 0;
         transform: translateY(40px);
      }

      to {
         opacity: 1;
         transform: translateY(0);
      }
   }

   /* Features Section */
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

   .feature-text h4 {
      margin-bottom: 0.2rem;
      font-weight: 600;
   }

   .feature-text p {
      opacity: 0.7;
      font-size: 0.9rem;
   }

   /* Responsive adjustments */
   @media (max-width: 768px) {
      .hero-title {
         font-size: 2.5rem;
      }

      .hero-subtitle {
         font-size: 1.1rem;
      }
   }
</style>

<!-- Hero Section -->
<section class="hero-section">
   <div class="hero-content">
      <div class="glass-card">
         <h1 class="hero-title">Experience the Journey, Redefined.</h1>
         <p class="hero-subtitle">Premium Car Rentals & Professional Driver Services at your fingertips. Luxury,
            comfort, and reliability for every destination.</p>
         <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
            <a href="viewcars.php" class="btn btn-premium btn-gradient mb-3 mb-md-0 mx-2">
               <i class="fas fa-car mr-2"></i> Find a Car
            </a>
            <a href="login.php" class="btn btn-premium btn-outline-glass mx-2">
               <i class="fas fa-key mr-2"></i> Join Us
            </a>
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

<!-- Features Section -->
<section class="features-section">
   <div class="container">
      <div class="row align-items-center">
         <div class="col-lg-6">
            <div class="pr-lg-5">
               <h2 class="mb-4 display-4 font-weight-bold">Why Choose CAR2GO?</h2>
               <p class="mb-5 lead opacity-8">We focus on trust and safety above everything else, ensuring every ride is
                  a premium experience.</p>

               <div class="feature-item">
                  <div class="feature-check">
                     <i class="fas fa-check"></i>
                  </div>
                  <div class="feature-text">
                     <h4>Trusted Drivers</h4>
                     <p>Every driver goes through extensive background checks and professional training.</p>
                  </div>
               </div>

               <div class="feature-item">
                  <div class="feature-check">
                     <i class="fas fa-shield-alt"></i>
                  </div>
                  <div class="feature-text">
                     <h4>24/7 Safety Support</h4>
                     <p>Our team is available round the clock to ensure your safety and comfort.</p>
                  </div>
               </div>

               <div class="feature-item">
                  <div class="feature-check">
                     <i class="fas fa-medal"></i>
                  </div>
                  <div class="feature-text">
                     <h4>Premium Collection</h4>
                     <p>Access to exclusively maintained, high-end vehicle fleet for all occasions.</p>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-lg-6 mt-5 mt-lg-0">
            <div class="glass-card" style="padding: 1rem; overflow: hidden; border-radius: 30px;">
               <img src="images/bg2.jpg" alt="Premium Experience" class="img-fluid"
                  style="border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
            </div>
         </div>
      </div>
   </div>
</section>

<?php require_once 'templates/footer.php'; ?>