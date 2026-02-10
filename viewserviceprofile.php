<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();

$u_id = $_SESSION['l_id'];
$s_id = isset($_GET['sl_id']) ? (int) $_GET['sl_id'] : 0;

if ($s_id <= 0) {
   redirect_with_message('viewservicee1.php', 'Invalid service center selected.', 'danger');
}

// Fetch Service Center Details
$query = "SELECT * FROM service_reg WHERE sl_id = ?";
$center = db_fetch_one($con, $query, "i", [$s_id]);

if (!$center) {
   redirect_with_message('viewservicee1.php', 'Service center not found.', 'danger');
}

// Fetch Services offered by this center
$services_query = "SELECT * FROM service_details WHERE sel_id = ?";
$offered_services = db_fetch_all($con, $services_query, "i", [$s_id]);

// Fetch related service centers
$related_query = "SELECT * FROM service_reg WHERE sl_id != ? ORDER BY RAND() LIMIT 3";
$related_centers = db_fetch_all($con, $related_query, "i", [$s_id]);

$error_message = '';
$success_message = '';

// Handle Service Request Submission
if (isset($_POST['submit'])) {
   if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
      $error_message = 'Invalid request.';
   } else {
      $v_name = sanitize_input($_POST['v_name']);
      $s_name = sanitize_input($_POST['s_name']);
      $date = $_POST['date'];
      $rev = sanitize_input($_POST['rev']);

      if (empty($v_name) || empty($s_name) || empty($date)) {
         $error_message = 'Please fill all required fields.';
      } else if (strtotime($date) < strtotime(date('Y-m-d'))) {
         $error_message = 'Service date cannot be in the past.';
      } else {
         $book_query = "INSERT INTO servicereq (u_id, s_id, v_name, s_name, date, rev, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
         if (db_execute($con, $book_query, "iisssss", [$u_id, $s_id, $v_name, $s_name, $date, $rev, 'Pending'])) {
            redirect_with_message('user/bookings.php', 'Service request sent! View details in your bookings.', 'success');
         } else {
            $error_message = 'Failed to process request. Please try again.';
         }
      }
   }
}

$page_title = $center['s_name'] . ' - Verified Service Partner';
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
      background: url('<?php echo !empty($center['s_rc']) ? 'uploads/services/' . $center['s_rc'] : 'images/default-service.jpg'; ?>') center/cover;
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
      height: 350px;
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

   .booking-input,
   .booking-select,
   .booking-textarea {
      border: none;
      background: transparent;
      width: 100%;
      font-weight: 700;
      color: #1e293b;
      padding: 5px 0;
      outline: none;
      font-size: 1.0rem;
   }

   .booking-select {
      cursor: pointer;
   }

   .booking-textarea {
      resize: none;
      font-weight: 500;
      font-size: 0.95rem;
   }

   .btn-book-visual {
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

   .btn-book-visual:hover {
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

   .service-item-row {
      padding: 15px;
      border-radius: 12px;
      background: #f8fafc;
      margin-bottom: 15px;
      border: 1px solid #e2e8f0;
      transition: all 0.2s;
   }

   .service-item-row:hover {
      background: #eff6ff;
      border-color: #3b82f6;
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

      .main-img-card img {
         height: 250px;
      }
   }

   @media (max-width: 768px) {
      .info-grid {
         grid-template-columns: 1fr;
      }
   }
</style>

<!-- Hero Section -->
<div class="page-hero">
   <div class="container hero-content">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Home</a></li>
            <li class="breadcrumb-item"><a href="viewservicee1.php" class="text-white-50">Service Centers</a></li>
            <li class="breadcrumb-item active text-white" aria-current="page"><?php echo e($center['s_name']); ?></li>
         </ol>
      </nav>
      <div class="row align-items-end">
         <div class="col-md-8">
            <h1 class="display-4 font-weight-bold mb-2"><?php echo e($center['s_name']); ?></h1>
            <p class="lead opacity-8 mb-0">
               <i class="fas fa-map-marker-alt mr-2"></i> <?php echo e($center['s_address']); ?>
            </p>
         </div>
         <div class="col-md-4 text-md-right d-none d-md-block">
            <div class="d-inline-flex align-items-center bg-white text-dark px-3 py-2 rounded-pill font-weight-bold">
               <i class="fas fa-star text-warning mr-1"></i> 4.8 Rating
            </div>
         </div>
      </div>
   </div>
</div>

<div class="container pb-5" style="z-index: 10; position: relative;">

   <?php if ($success_message): ?>
      <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm">
         <i class="fas fa-check-circle fa-2x mr-3"></i>
         <div class="ml-3"><?php echo e($success_message); ?></div>
      </div>
   <?php endif; ?>

   <?php if ($error_message): ?>
      <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm">
         <i class="fas fa-exclamation-circle fa-2x mr-3"></i>
         <div class="ml-3"><?php echo e($error_message); ?></div>
      </div>
   <?php endif; ?>

   <div class="row">
      <!-- Left Column: Center Info -->
      <div class="col-lg-8">
         <div class="main-img-card">
            <?php
            $img_path = !empty($center['s_rc']) ? 'uploads/services/' . $center['s_rc'] : 'images/default-service.jpg';
            ?>
            <img src="<?php echo $img_path; ?>" alt="Center Image" onerror="this.src='images/default-service.jpg'">
         </div>

         <div class="detail-card">
            <h4 class="font-weight-bold mb-4">Service Center Information</h4>
            <div class="info-grid">
               <div class="info-item">
                  <div class="info-icon"><i class="fas fa-phone"></i></div>
                  <div>
                     <div class="text-muted small">Contact Number</div>
                     <div class="font-weight-bold"><?php echo e($center['s_phone']); ?></div>
                  </div>
               </div>
               <div class="info-item">
                  <div class="info-icon"><i class="fas fa-envelope"></i></div>
                  <div>
                     <div class="text-muted small">Official Email</div>
                     <div class="font-weight-bold"><?php echo e($center['s_email']); ?></div>
                  </div>
               </div>
               <div class="info-item">
                  <div class="info-icon"><i class="fas fa-map-pin"></i></div>
                  <div>
                     <div class="text-muted small">Pin Code</div>
                     <div class="font-weight-bold"><?php echo e($center['s_pincode']); ?></div>
                  </div>
               </div>
               <div class="info-item">
                  <div class="info-icon"><i class="fas fa-clock"></i></div>
                  <div>
                     <div class="text-muted small">Working Hours</div>
                     <div class="font-weight-bold">Mon-Sat, 9AM-7PM</div>
                  </div>
               </div>
            </div>

            <h5 class="font-weight-bold mb-3 mt-5">Services & Pricing</h5>
            <?php if (!empty($offered_services)): ?>
               <div class="services-list">
                  <?php foreach ($offered_services as $service): ?>
                     <div class="service-item-row d-flex justify-content-between align-items-center">
                        <div>
                           <div class="font-weight-bold text-dark"><?php echo e($service['se_name']); ?></div>
                           <div class="small text-muted"><?php echo e($service['se_details']); ?></div>
                        </div>
                        <div class="text-primary font-weight-bold h5 mb-0">₹<?php echo number_format($service['se_price']); ?>
                        </div>
                     </div>
                  <?php endforeach; ?>
               </div>
            <?php else: ?>
               <div class="alert alert-light border">
                  No specific pricing listed. Please contact for a quote.
               </div>
            <?php endif; ?>

            <h5 class="font-weight-bold mb-3 mt-5">Verified Documents</h5>
            <div class="row">
               <div class="col-md-6 mb-3">
                  <a href="uploads/services/<?php echo e($center['s_licence']); ?>" target="_blank"
                     class="btn btn-outline-secondary btn-block text-left p-3 rounded-lg" style="border-radius: 12px;">
                     <i class="fas fa-file-contract text-primary mr-2"></i> Operating Licence
                  </a>
               </div>
               <div class="col-md-6 mb-3">
                  <div class="btn btn-light btn-block text-left p-3 rounded-lg disabled"
                     style="opacity: 0.7; cursor: default; border-radius: 12px;">
                     <i class="fas fa-check-circle text-success mr-2"></i> Govt. Approved
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Right Column: Booking Form -->
      <div class="col-lg-4">
         <div class="booking-card animate__animated animate__fadeInRight animate__delay-1s">
            <div class="booking-header">
               <i class="fas fa-tools fa-3x text-white-50 mb-3"></i>
               <h3 class="font-weight-bold mb-0 text-white">Book Service</h3>
               <p class="text-white-50 small mb-0 mt-2 font-weight-bold letter-spacing-1">SCHEDULE A VISIT</p>
            </div>

            <div class="booking-body p-4">
               <form action="" method="post">
                  <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                  <div class="booking-form-group">
                     <label class="booking-label"><i class="fas fa-car mr-2 text-primary"></i> Vehicle Details</label>
                     <input type="text" name="v_name" class="booking-input" placeholder="e.g. Toyota Innova" required>
                  </div>

                  <div class="booking-form-group">
                     <label class="booking-label"><i class="fas fa-list-ul mr-2 text-primary"></i> Service Type</label>
                     <select name="s_name" class="booking-select" required>
                        <option value="" disabled selected>Select Service</option>
                        <?php if (!empty($offered_services)): ?>
                           <?php foreach ($offered_services as $service): ?>
                              <option value="<?php echo e($service['se_name']); ?>"><?php echo e($service['se_name']); ?> -
                                 ₹<?php echo number_format($service['se_price']); ?></option>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <option value="General Service">General Service</option>
                           <option value="Engine Repair">Engine Repair</option>
                           <option value="Body Work">Body Work & Paint</option>
                           <option value="AC Maintenance">AC Maintenance</option>
                           <option value="Car Wash">Water Wash & Spa</option>
                        <?php endif; ?>
                     </select>
                  </div>

                  <div class="booking-form-group">
                     <label class="booking-label"><i class="far fa-calendar-alt mr-2 text-primary"></i> Preferred
                        Date</label>
                     <input type="date" name="date" class="booking-input" min="<?php echo date('Y-m-d'); ?>" required>
                  </div>

                  <div class="booking-form-group mb-4">
                     <label class="booking-label"><i class="fas fa-comment-alt mr-2 text-primary"></i>
                        Description</label>
                     <textarea name="rev" class="booking-textarea" rows="2"
                        placeholder="Describe the issue..."></textarea>
                  </div>

                  <button type="submit" name="submit" class="btn btn-primary btn-block rounded-xl btn-book-visual">
                     CONFIRM BOOKING <i class="fas fa-check-circle ml-2"></i>
                  </button>
               </form>

               <div class="text-center mt-4 pt-4 border-top">
                  <div class="d-flex align-items-center justify-content-center text-muted mb-2">
                     <i class="fas fa-shield-alt text-success fa-lg mr-2"></i>
                     <span class="font-weight-bold text-dark">Genuine Parts</span>
                  </div>
                  <p class="text-muted extra-small mb-0">90-day service warranty included.</p>
               </div>
            </div>
         </div>

         <div class="card border-0 shadow-sm rounded-lg p-4 mt-4">
            <h6 class="font-weight-bold mb-3">Why choose us?</h6>
            <ul class="pl-3 mb-0 text-muted small">
               <li class="mb-2">Certified mechanics</li>
               <li class="mb-2">Transparent pricing</li>
               <li class="mb-2">On-time delivery</li>
               <li>90-day service warranty</li>
            </ul>
         </div>
      </div>
   </div>

   <!-- Related Centers -->
   <?php if (!empty($related_centers)): ?>
      <div class="mt-5 pt-4">
         <h3 class="font-weight-bold mb-4">Other Nearby Centers</h3>
         <div class="row">
            <?php foreach ($related_centers as $rc): ?>
               <div class="col-md-4 mb-4">
                  <div class="card border-0 shadow-sm h-100"
                     style="border-radius: 20px; overflow: hidden; transition: transform 0.3s; background: white;">
                     <div style="height: 160px; overflow: hidden; position: relative;">
                        <img
                           src="<?php echo !empty($rc['s_rc']) ? 'uploads/services/' . $rc['s_rc'] : 'images/default-service.jpg'; ?>"
                           class="w-100 h-100" style="object-fit: cover;" onerror="this.src='images/default-service.jpg'">
                     </div>
                     <div class="card-body p-4 text-center">
                        <h5 class="font-weight-bold mb-2"><?php echo e($rc['s_name']); ?></h5>
                        <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt text-primary mr-1"></i>
                           <?php echo e($rc['s_pincode']); ?></p>
                        <a href="viewserviceprofile.php?sl_id=<?php echo $rc['sl_id']; ?>"
                           class="btn btn-outline-primary btn-block rounded-pill font-weight-bold">View Center</a>
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