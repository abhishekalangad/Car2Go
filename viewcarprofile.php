<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();

$l_id = $_SESSION['l_id'];
$r_id = isset($_GET['r_id']) ? (int) $_GET['r_id'] : 0;

if ($r_id <= 0) {
   redirect_with_message('viewcars.php', 'Invalid car selected.', 'danger');
}

// Fetch car and owner details
$query = "SELECT r.*, u.u_name, u.u_address, u.u_phone as owner_phone 
          FROM rent r 
          INNER JOIN user_reg u ON r.rl_id = u.ul_id 
          WHERE r.r_id = ? AND r.r_status = 'approve'";
$car = db_fetch_one($con, $query, "i", [$r_id]);

if (!$car) {
   redirect_with_message('viewcars.php', 'Car not found or not available.', 'danger');
}

// Fetch related cars (other cars from the same owner or same city/type)
$related_query = "SELECT * FROM rent WHERE r_status = 'approve' AND r_id != ? ORDER BY RAND() LIMIT 3";
$related_cars = db_fetch_all($con, $related_query, "i", [$r_id]);

$error_message = '';
$success_message = '';

// Handle Booking Submission
if (isset($_POST['submit'])) {
   if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
      $error_message = 'Invalid request.';
   } else {
      $day1 = $_POST['day1'];
      $day2 = $_POST['day2'];
      $bo_id = $l_id; // Current logged-in user (booker)
      $br_id = $car['rl_id']; // Car owner ID (from the car record)

      if (empty($day1) || empty($day2)) {
         $error_message = 'Please select both start and end dates.';
      } else if (strtotime($day1) < strtotime(date('Y-m-d'))) {
         $error_message = 'Start date cannot be in the past.';
      } else if (strtotime($day2) < strtotime($day1)) {
         $error_message = 'End date cannot be before start date.';
      } else {
         $book_query = "INSERT INTO bookcar (bo_id, br_id, b_day1, b_day2, b_status) VALUES (?, ?, ?, ?, ?)";
         if (db_execute($con, $book_query, "iisss", [$bo_id, $r_id, $day1, $day2, 'Booked'])) {
            redirect_with_message('user/bookings.php', 'Car booking successful! You can track it here.', 'success');
         } else {
            $error_message = 'Failed to process booking. Please try again.';
         }
      }
   }
}

$page_title = $car['r_company'] . ' ' . $car['r_mname'] . ' - Details';
include 'templates/header.php';
?>

<style>
   /* Premium Page Styles */
   .page-hero {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      padding: 5rem 0 8rem;
      color: white;
      position: relative;
      overflow: hidden;
      margin-bottom: -50px;
   }

   .page-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('images/<?php echo !empty($car['r_car']) ? $car['r_car'] : 'default-car.jpg'; ?>') center/cover;
      opacity: 0.2;
      filter: blur(8px);
      z-index: 0;
   }

   .hero-content {
      position: relative;
      z-index: 2;
   }

   .main-img-card {
      background: white;
      border-radius: 20px;
      padding: 10px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
      margin-bottom: 2rem;
   }

   .main-img-card img {
      border-radius: 16px;
      width: 100%;
      height: auto;
      object-fit: cover;
   }

   .detail-card {
      background: white;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      border: 1px solid #f1f5f9;
      height: 100%;
      margin-bottom: 2rem;
   }

   .spec-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1.5rem;
   }

   .spec-item {
      display: flex;
      align-items: center;
      padding: 10px;
      background: #f8fafc;
      border-radius: 12px;
   }

   .spec-icon {
      width: 40px;
      height: 40px;
      background: #eff6ff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #3b82f6;
      margin-right: 15px;
      font-size: 1.1rem;
   }

   .booking-card {
      background: white;
      border: none;
      border-radius: 24px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      overflow: hidden;
      position: sticky;
      top: 100px;
      transition: transform 0.3s ease;
   }

   .booking-card:hover {
      transform: translateY(-5px);
   }

   .booking-header {
      background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
      padding: 3rem 2rem;
      color: white;
      text-align: center;
      position: relative;
   }

   .booking-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 70%);
      pointer-events: none;
   }

   .price-large {
      font-size: 3.5rem;
      font-weight: 800;
      line-height: 1;
      text-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      margin: 0.5rem 0;
      display: block;
   }

   .booking-form-group {
      background: #f8fafc;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      padding: 0.75rem 1rem;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      margin-bottom: 1rem;
      position: relative;
   }

   .booking-form-group:focus-within {
      background: white;
      border-color: #3b82f6;
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
      transform: translateY(-1px);
   }

   .booking-label {
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      color: #64748b;
      margin-bottom: 2px;
      display: flex;
      align-items: center;
   }

   .booking-input {
      border: none;
      background: transparent;
      width: 100%;
      font-weight: 700;
      color: #1e293b;
      padding: 5px 0;
      outline: none;
      font-size: 1.1rem;
   }

   .btn-reserve-visual {
      background: linear-gradient(135deg, #2563eb, #1d4ed8);
      border: none;
      padding: 1.25rem;
      font-size: 1.1rem;
      font-weight: 800;
      letter-spacing: 1px;
      text-transform: uppercase;
      box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
      transition: all 0.3s;
   }

   .btn-reserve-visual:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 30px rgba(37, 99, 235, 0.5);
      background: linear-gradient(135deg, #3b82f6, #2563eb);
   }

   /* Helpers */
   .d-flex {
      display: flex;
   }

   .align-items-center {
      align-items: center;
   }

   .justify-content-between {
      justify-content: space-between;
   }

   .doc-link {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      background: #f1f5f9;
      color: #334155;
      border-radius: 10px;
      margin-bottom: 10px;
      text-decoration: none;
      transition: all 0.2s;
      font-weight: 600;
      font-size: 0.9rem;
   }

   .doc-link:hover {
      background: #e2e8f0;
      color: #2563eb;
      transform: translateX(5px);
   }

   /* Mobile Optimization */
   @media (max-width: 991px) {
      .booking-card {
         position: static;
         margin-top: 2rem;
      }

      .page-hero {
         padding: 3rem 0 5rem;
      }

      .display-4 {
         font-size: 2.5rem;
      }
   }

   @media (max-width: 768px) {
      .spec-grid {
         grid-template-columns: 1fr;
      }

      .price-large {
         font-size: 2.5rem;
      }
   }
</style>

<!-- Hero Section -->
<div class="page-hero">
   <div class="container hero-content">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Home</a></li>
            <li class="breadcrumb-item"><a href="viewcars.php" class="text-white-50">Cars</a></li>
            <li class="breadcrumb-item active text-white" aria-current="page"><?php echo e($car['r_mname']); ?></li>
         </ol>
      </nav>
      <div class="row align-items-end">
         <div class="col-md-8">
            <h1 class="display-4 font-weight-bold mb-2"><?php echo e($car['r_company']); ?>
               <?php echo e($car['r_mname']); ?>
            </h1>
            <p class="lead opacity-8 mb-0">
               <i class="fas fa-map-marker-alt mr-2"></i> <?php echo e($car['r_pincode']); ?>
               <span class="mx-2">•</span>
               <?php echo e($car['r_year']); ?> Model
            </p>
         </div>
         <div class="col-md-4 text-md-right d-none d-md-block">
            <div class="badge badge-light px-3 py-2 rounded-pill font-weight-bold">
               <i class="fas fa-check-circle text-success mr-1"></i> Verified Car
            </div>
         </div>
      </div>
   </div>
</div>

<div class="container pb-5" style="z-index: 10; position: relative;">
   <?php if ($success_message): ?>
      <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm">
         <i class="fas fa-check-circle fa-2x mr-3"></i>
         <div><?php echo e($success_message); ?></div>
      </div>
   <?php endif; ?>

   <div class="row">
      <!-- Left Column: Details -->
      <div class="col-lg-8">
         <div class="main-img-card">
            <img src="<?php echo !empty($car['r_car']) ? 'images/' . $car['r_car'] : 'images/default-car.jpg'; ?>"
               alt="Car Image" class="w-100" onerror="this.src='images/default-car.jpg'">
         </div>

         <!-- Specs Grid -->
         <div class="detail-card">
            <h4 class="font-weight-bold mb-4">Vehicle Specifications</h4>
            <div class="spec-grid">
               <div class="spec-item">
                  <div class="spec-icon"><i class="fas fa-calendar-alt"></i></div>
                  <div>
                     <div class="text-muted small">Model Year</div>
                     <div class="font-weight-bold"><?php echo e($car['r_year']); ?></div>
                  </div>
               </div>
               <div class="spec-item">
                  <div class="spec-icon"><i class="fas fa-users"></i></div>
                  <div>
                     <div class="text-muted small">Seating Capacity</div>
                     <div class="font-weight-bold"><?php echo e($car['r_seat']); ?> Persons</div>
                  </div>
               </div>
               <div class="spec-item">
                  <div class="spec-icon"><i class="fas fa-gas-pump"></i></div>
                  <div>
                     <div class="text-muted small">Fuel Rate</div>
                     <div class="font-weight-bold">₹<?php echo e($car['r_ppkm']); ?> / KM</div>
                  </div>
               </div>
               <div class="spec-item">
                  <div class="spec-icon"><i class="fas fa-car"></i></div>
                  <div>
                     <div class="text-muted small">Registration</div>
                     <div class="font-weight-bold"><?php echo e($car['r_number']); ?></div>
                  </div>
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-md-6">
               <div class="detail-card">
                  <h5 class="font-weight-bold mb-3"><i class="fas fa-info-circle text-primary mr-2"></i> About Car</h5>
                  <p class="text-muted small mb-0"><?php echo nl2br(e($car['r_custatus'])); ?></p>
                  <hr>
                  <h6 class="small font-weight-bold">Additional Info</h6>
                  <p class="text-muted small mb-0">
                     <?php echo !empty($car['r_addinfo']) ? nl2br(e($car['r_addinfo'])) : 'N/A'; ?>
                  </p>
               </div>
            </div>
            <div class="col-md-6">
               <div class="detail-card">
                  <h5 class="font-weight-bold mb-3"><i class="fas fa-file-alt text-primary mr-2"></i> Documents</h5>
                  <?php if (!empty($car['r_tax'])): ?>
                     <a href="images/<?php echo e($car['r_tax']); ?>" target="_blank" class="doc-link">
                        <i class="fas fa-file-contract mr-2"></i> Registration (RC)
                     </a>
                  <?php endif; ?>
                  <?php if (!empty($car['r_insurance'])): ?>
                     <a href="images/<?php echo e($car['r_insurance']); ?>" target="_blank" class="doc-link">
                        <i class="fas fa-shield-alt mr-2"></i> Insurance
                     </a>
                  <?php endif; ?>
                  <?php if (!empty($car['r_polution'])): ?>
                     <a href="images/<?php echo e($car['r_polution']); ?>" target="_blank" class="doc-link">
                        <i class="fas fa-smog mr-2"></i> Pollution Cert
                     </a>
                  <?php endif; ?>
               </div>
            </div>
         </div>

         <!-- Owner Info -->
         <div class="detail-card">
            <h5 class="font-weight-bold mb-3">Car Owner</h5>
            <div class="d-flex align-items-center">
               <div class="bg-light rounded-circle p-3 mr-3">
                  <i class="fas fa-user-tie fa-2x text-primary"></i>
               </div>
               <div>
                  <h5 class="mb-0 font-weight-bold"><?php echo e($car['u_name']); ?></h5>
                  <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt"></i>
                     <?php echo e($car['u_address']); ?></p>
               </div>
               <div class="ml-auto">
                  <a href="tel:<?php echo e($car['r_phone']); ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                     <i class="fas fa-phone mr-1"></i> Call Owner
                  </a>
               </div>
            </div>
         </div>
      </div>

      <!-- Right Column: Booking Form -->
      <div class="col-lg-4">
         <div class="booking-card animate__animated animate__fadeInRight animate__delay-1s">
            <div class="booking-header">
               <div class="small text-white-50 text-uppercase font-weight-bold letter-spacing-1 mb-2">Daily Rate</div>
               <span class="price-large">₹<?php echo number_format($car['rent_amt']); ?></span>
               <div class="badge badge-light text-primary font-weight-bold px-3 py-1 rounded-pill mt-2 small">
                  <i class="fas fa-bolt mr-1"></i> Instant Booking
               </div>
            </div>

            <div class="booking-body p-4">
               <?php if ($error_message): ?>
                  <div class="alert alert-danger small mb-3 rounded-lg border-0 shadow-sm"><?php echo e($error_message); ?>
                  </div>
               <?php endif; ?>

               <form action="" method="post" id="bookingForm">
                  <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                  <div class="booking-form-group">
                     <label class="booking-label"><i class="far fa-calendar-alt mr-2 text-primary"></i> Pick-up
                        Date</label>
                     <input type="date" id="day1" name="day1" class="booking-input" min="<?php echo date('Y-m-d'); ?>"
                        required>
                  </div>

                  <div class="booking-form-group mb-2">
                     <label class="booking-label"><i class="far fa-calendar-check mr-2 text-primary"></i> Return
                        Date</label>
                     <input type="date" id="day2" name="day2" class="booking-input" min="<?php echo date('Y-m-d'); ?>"
                        required>
                  </div>

                  <!-- Price Summary -->
                  <div id="priceSummary" class="mb-4 d-none">
                     <div class="bg-light rounded-lg p-3">
                        <div class="d-flex justify-content-between mb-1 small">
                           <span class="text-muted">Duration:</span>
                           <span id="displayDays" class="font-weight-bold text-dark">0 days</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small border-bottom pb-2">
                           <span class="text-muted">Daily Rate:</span>
                           <span
                              class="font-weight-bold text-dark">₹<?php echo number_format($car['rent_amt']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                           <span class="font-weight-bold text-dark">Estimated Total:</span>
                           <span id="displayTotal" class="h5 font-weight-bold text-primary mb-0">₹0</span>
                        </div>
                     </div>
                  </div>

                  <button type="submit" name="submit" class="btn btn-primary btn-block rounded-xl btn-reserve-visual">
                     RESERVE NOW <i class="fas fa-arrow-right ml-2"></i>
                  </button>
               </form>

               <div class="text-center mt-4 pt-4 border-top">
                  <div class="d-flex align-items-center justify-content-center text-muted mb-2">
                     <i class="fas fa-shield-alt text-success fa-lg mr-2"></i>
                     <span class="font-weight-bold text-dark">100% Secure</span>
                  </div>
                  <p class="text-muted extra-small mb-0">Payment handled directly with owner.</p>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Related Vehicles -->
   <?php if (!empty($related_cars)): ?>
      <div class="mt-5 pt-4">
         <h3 class="font-weight-bold mb-4">You Might Also Like</h3>
         <div class="row">
            <?php foreach ($related_cars as $rc): ?>
               <div class="col-md-4 mb-4">
                  <div class="card border-0 shadow-sm h-100"
                     style="border-radius: 16px; overflow: hidden; transition: transform 0.3s; background: white;">
                     <div style="height: 180px; overflow: hidden; position: relative;">
                        <img src="<?php echo !empty($rc['r_car']) ? 'images/' . $rc['r_car'] : 'images/default-car.jpg'; ?>"
                           class="w-100 h-100" style="object-fit: cover;" onerror="this.src='images/default-car.jpg'">
                        <div style="position: absolute; top: 15px; right: 15px;">
                           <span class="badge badge-primary px-3 py-2 rounded-pill shadow-sm">₹
                              <?php echo number_format($rc['rent_amt']); ?>/day
                           </span>
                        </div>
                     </div>
                     <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-2">
                           <?php echo e($rc['r_company'] . ' ' . $rc['r_mname']); ?>
                        </h5>
                        <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt text-primary mr-1"></i>
                           <?php echo e($rc['r_pincode']); ?>
                        </p>
                        <a href="viewcarprofile.php?r_id=<?php echo $rc['r_id']; ?>"
                           class="btn btn-outline-primary btn-block rounded-pill font-weight-bold">View Details</a>
                     </div>
                  </div>
               </div>
            <?php endforeach; ?>
         </div>
      </div>
   <?php endif; ?>
</div>

<style>
   .card:hover {
      transform: translateY(-5px);
   }
</style>

<?php include 'templates/footer.php'; ?>

<script>
   $(document).ready(function () {
      const day1Input = $('#day1');
      const day2Input = $('#day2');
      const priceSummary = $('#priceSummary');
      const displayDays = $('#displayDays');
      const displayTotal = $('#displayTotal');
      const dailyRate = <?php echo (int) $car['rent_amt']; ?>;

      function calculatePrice() {
         const date1 = new Date(day1Input.val());
         const date2 = new Date(day2Input.val());

         if (!isNaN(date1.getTime()) && !isNaN(date2.getTime())) {
            // Set date2 min to date1
            day2Input.attr('min', day1Input.val());

            const diffTime = date2.getTime() - date1.getTime();
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Inclusive of start day

            if (diffDays > 0) {
               const total = diffDays * dailyRate;
               displayDays.text(diffDays + (diffDays === 1 ? ' day' : ' days'));
               displayTotal.text('₹' + total.toLocaleString());
               priceSummary.removeClass('d-none').addClass('animate__animated animate__fadeIn');
            } else {
               priceSummary.addClass('d-none');
            }
         } else {
            priceSummary.addClass('d-none');
         }
      }

      day1Input.on('change', calculatePrice);
      day2Input.on('change', calculatePrice);
   });
</script>