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
   .profile-header {
      background: var(--bg-dark);
      padding: 40px 0;
      margin-top: -20px;
      color: white;
   }

   .main-img-container {
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
   }

   .detail-card {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      height: 100%;
      border: 1px solid #f1f5f9;
   }

   .spec-item {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px solid #f1f5f9;
   }

   .spec-item:last-child {
      border-bottom: none;
   }

   .spec-label {
      color: #64748b;
      font-weight: 500;
   }

   .spec-value {
      font-weight: 700;
      color: var(--bg-dark);
   }

   .booking-sticky {
      position: sticky;
      top: 100px;
   }

   .doc-link {
      display: block;
      padding: 15px;
      background: #f8fafc;
      border-radius: 12px;
      margin-bottom: 10px;
      color: var(--bg-dark);
      text-decoration: none;
      transition: all 0.2s ease;
      border: 1px solid #e2e8f0;
   }

   .doc-link:hover {
      background: #eff6ff;
      border-color: var(--primary-color);
      color: var(--primary-color);
      text-decoration: none;
   }
</style>

<div class="profile-header">
   <div class="container">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb bg-transparent p-0 mb-4">
            <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Home</a></li>
            <li class="breadcrumb-item"><a href="viewcars.php" class="text-white-50">Cars</a></li>
            <li class="breadcrumb-item active text-white" aria-current="page"><?php echo e($car['r_mname']); ?></li>
         </ol>
      </nav>
      <div class="d-flex justify-content-between align-items-end">
         <div>
            <h1 class="display-4 font-weight-bold mb-0"><?php echo e($car['r_company']); ?>
               <?php echo e($car['r_mname']); ?>
            </h1>
            <p class="lead opacity-7 mb-0">Registered in <?php echo e($car['r_year']); ?> •
               <?php echo e($car['r_seat']); ?> Seater
            </p>
         </div>
         <div class="text-right">
            <div class="h3 font-weight-bold text-primary mb-0">₹<?php echo e($car['rent_amt']); ?></div>
            <div class="small opacity-5 text-uppercase">Per Day</div>
         </div>
      </div>
   </div>
</div>

<div class="container py-5">
   <?php if ($success_message): ?>
      <div class="alert alert-success d-flex align-items-center mb-5">
         <i class="fas fa-check-circle fa-2x mr-3"></i>
         <div><?php echo e($success_message); ?></div>
      </div>
   <?php endif; ?>

   <div class="row">
      <!-- Left Column: Details -->
      <div class="col-lg-8">
         <div class="main-img-container mb-5">
            <img src="images/<?php echo e($car['r_car']); ?>" class="w-100" alt="Car Image">
         </div>

         <div class="row mb-5">
            <div class="col-md-6 mb-4">
               <div class="detail-card">
                  <h4 class="font-weight-bold mb-4">Vehicle Specifications</h4>
                  <div class="spec-item">
                     <span class="spec-label">Model Year</span>
                     <span class="spec-value"><?php echo e($car['r_year']); ?></span>
                  </div>
                  <div class="spec-item">
                     <span class="spec-label">Seating</span>
                     <span class="spec-value"><?php echo e($car['r_seat']); ?> Persons</span>
                  </div>
                  <div class="spec-item">
                     <span class="spec-label">Rent/KM</span>
                     <span class="spec-value">₹<?php echo e($car['r_ppkm']); ?></span>
                  </div>
                  <div class="spec-item">
                     <span class="spec-label">Plate No</span>
                     <span class="spec-value"><?php echo e($car['r_number']); ?></span>
                  </div>
                  <div class="spec-item">
                     <span class="spec-label">Location</span>
                     <span class="spec-value"><?php echo e($car['r_pincode']); ?></span>
                  </div>
               </div>
            </div>
            <div class="col-md-6 mb-4">
               <div class="detail-card">
                  <h4 class="font-weight-bold mb-4">Verification Docs</h4>
                  <?php if (!empty($car['r_tax'])): ?>
                     <a href="images/<?php echo e($car['r_tax']); ?>" target="_blank" class="doc-link">
                        <i class="fas fa-file-contract mr-2"></i> Registration Cert (RC)
                     </a>
                  <?php endif; ?>
                  <?php if (!empty($car['r_insurance'])): ?>
                     <a href="images/<?php echo e($car['r_insurance']); ?>" target="_blank" class="doc-link">
                        <i class="fas fa-shield-alt mr-2"></i> Insurance Document
                     </a>
                  <?php endif; ?>
                  <?php if (!empty($car['r_polution'])): ?>
                     <a href="images/<?php echo e($car['r_polution']); ?>" target="_blank" class="doc-link">
                        <i class="fas fa-smog mr-2"></i> Pollution Certificate
                     </a>
                  <?php endif; ?>
                  <div class="alert alert-info small mt-3">
                     <i class="fas fa-info-circle mr-2"></i> Documents are verified by CAR2GO Admin.
                  </div>
               </div>
            </div>
         </div>

         <div class="detail-card mb-5">
            <h4 class="font-weight-bold mb-4">About this Car</h4>
            <div class="mb-4">
               <h6 class="text-uppercase small font-weight-bold text-muted">Condition & Status</h6>
               <p><?php echo nl2br(e($car['r_custatus'])); ?></p>
            </div>
            <div class="mb-4">
               <h6 class="text-uppercase small font-weight-bold text-muted">Accident History</h6>
               <p><?php echo !empty($car['r_acchistory']) ? nl2br(e($car['r_acchistory'])) : 'No accidents reported.'; ?>
               </p>
            </div>
            <div>
               <h6 class="text-uppercase small font-weight-bold text-muted">Additional Info</h6>
               <p><?php echo !empty($car['r_addinfo']) ? nl2br(e($car['r_addinfo'])) : 'N/A'; ?></p>
            </div>
         </div>

         <!-- Owner Info -->
         <div class="detail-card">
            <div class="d-flex align-items-center mb-4">
               <div class="bg-light rounded-circle p-3 mr-3">
                  <i class="fas fa-user-tie fa-2x text-primary"></i>
               </div>
               <div>
                  <h5 class="mb-0 font-weight-bold"><?php echo e($car['u_name']); ?></h5>
                  <p class="text-muted small mb-0">Verified Car Owner</p>
               </div>
            </div>
            <div class="row">
               <div class="col-sm-6 mb-3">
                  <div class="small text-muted">Phone Number</div>
                  <div class="font-weight-bold"><?php echo e($car['r_phone']); ?></div>
               </div>
               <div class="col-sm-6 mb-3">
                  <div class="small text-muted">Location</div>
                  <div class="font-weight-bold"><?php echo e($car['u_address']); ?></div>
               </div>
            </div>
         </div>
      </div>

      <!-- Right Column: Booking Form -->
      <div class="col-lg-4">
         <div class="booking-sticky">
            <div class="detail-card" style="border-top: 5px solid var(--primary-color);">
               <h4 class="font-weight-bold mb-4 text-center">Book This Car</h4>

               <?php if ($error_message): ?>
                  <div class="alert alert-danger small"><?php echo e($error_message); ?></div>
               <?php endif; ?>

               <form action="" method="post" class="premium-form">
                  <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                  <div class="form-group mb-4">
                     <label class="small font-weight-bold text-uppercase">Pickup Date</label>
                     <input type="date" name="day1" class="form-control form-control-lg bg-light border-0"
                        min="<?php echo date('Y-m-d'); ?>" required>
                  </div>

                  <div class="form-group mb-4">
                     <label class="small font-weight-bold text-uppercase">Return Date</label>
                     <input type="date" name="day2" class="form-control form-control-lg bg-light border-0"
                        min="<?php echo date('Y-m-d'); ?>" required>
                  </div>

                  <div class="bg-primary-light p-4 rounded mb-4 text-center" style="background: #eff6ff;">
                     <div class="small text-muted mb-1">Estimated Total</div>
                     <div class="h4 font-weight-bold mb-0 text-primary">₹<?php echo e($car['rent_amt']); ?> <small>/
                           Day</small></div>
                     <small class="text-muted font-italic">Fuel & extra KM charges apply</small>
                  </div>

                  <button type="submit" name="submit"
                     class="btn btn-premium btn-gradient w-100 py-3 font-weight-bold shadow">
                     RESERVE NOW
                  </button>
               </form>

               <div class="text-center mt-4 pt-3 border-top">
                  <p class="small text-muted mb-0">No payment required now. The owner will review your request shortly.
                  </p>
               </div>
            </div>

            <div class="mt-4 p-4 text-center">
               <i class="fas fa-shield-alt text-success fa-2x mb-3"></i>
               <h6 class="font-weight-bold">Safe Booking Guarantee</h6>
               <p class="small text-muted">All our cars are sanitized and primary documents are verified by our team.
               </p>
            </div>
         </div>
      </div>
   </div>
</div>

<?php include 'templates/footer.php'; ?>