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
         // Note: DB table name is servicereq based on my previous dashboard analysis
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

<div class="profile-header text-white py-5"
   style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); margin-top: -20px;">
   <div class="container">
      <div class="row align-items-center">
         <div class="col-md-2 mb-4 mb-md-0">
            <div class="bg-white rounded-xl p-2 shadow">
               <img src="uploads/services/<?php echo e($center['s_rc'] ?: 'default-service.jpg'); ?>"
                  class="img-fluid rounded" alt="Logo">
            </div>
         </div>
         <div class="col-md-7">
            <h1 class="display-4 font-weight-bold mb-2"><?php echo e($center['s_name']); ?></h1>
            <div class="d-flex flex-wrap gap-3 opacity-8">
               <span class="mr-4"><i class="fas fa-map-marker-alt mr-2 text-primary"></i>
                  <?php echo e($center['s_address']); ?></span>
               <span><i class="fas fa-certificate mr-2 text-success"></i> CAR2GO Verified Partner</span>
            </div>
         </div>
         <div class="col-md-3 text-md-right mt-4 mt-md-0">
            <div class="h3 font-weight-bold text-warning mb-0">4.8 <small>/ 5.0</small></div>
            <div class="small opacity-5 text-uppercase">Partner Rating</div>
         </div>
      </div>
   </div>
</div>

<div class="container py-5">
   <div class="row">
      <!-- Center Details -->
      <div class="col-lg-8">
         <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show mb-5 py-3 shadow-sm border-0 rounded-lg">
               <i class="fas fa-check-circle mr-2"></i> <?php echo e($success_message); ?>
               <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
         <?php endif; ?>

         <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-5 py-3 shadow-sm border-0 rounded-lg">
               <i class="fas fa-exclamation-triangle mr-2"></i> <?php echo e($error_message); ?>
               <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
         <?php endif; ?>

         <div class="card border-0 shadow-sm rounded-xl p-5 mb-5">
            <h4 class="font-weight-bold mb-4">About Our Service Hub</h4>
            <p class="text-muted leading-relaxed">We provide premium automotive solutions with a focus on quality and
               customer satisfaction. Our facility is equipped with modern diagnostics tool and staffed by certified
               professionals.</p>
            <div class="row mt-5">
               <div class="col-md-6 mb-4">
                  <div class="d-flex align-items-start">
                     <div class="bg-primary-light text-primary rounded p-3 mr-3"><i class="fas fa-clock"></i></div>
                     <div>
                        <h6 class="font-weight-bold mb-1">Working Hours</h6>
                        <p class="small text-muted mb-0">Mon - Sat: 9:00 AM - 7:00 PM<br>Sunday: Closed</p>
                     </div>
                  </div>
               </div>
               <div class="col-md-6 mb-4">
                  <div class="d-flex align-items-start">
                     <div class="bg-primary-light text-primary rounded p-3 mr-3"><i class="fas fa-headset"></i></div>
                     <div>
                        <h6 class="font-weight-bold mb-1">Support Available</h6>
                        <p class="small text-muted mb-0">Call us at: <?php echo e($center['s_phone']); ?><br>Email:
                           help@car2go.com</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="card border-0 shadow-sm rounded-xl p-5">
            <h4 class="font-weight-bold mb-4">Partner Credentials</h4>
            <div class="row align-items-center">
               <div class="col-md-4">
                  <img src="uploads/services/<?php echo e($center['s_licence']); ?>" class="img-fluid rounded border"
                     alt="Licence">
               </div>
               <div class="col-md-8 mt-4 mt-md-0">
                  <h5>Operating Licence Verified</h5>
                  <p class="text-muted">This center has submitted all government required documents and is legally
                     authorized to provide vehicle maintenance services.</p>
                  <a href="uploads/services/<?php echo e($center['s_licence']); ?>" target="_blank"
                     class="btn btn-outline-primary btn-sm rounded-pill px-4">View Document</a>
               </div>
            </div>
         </div>
      </div>

      <!-- Request Form Sticky -->
      <div class="col-lg-4 mt-5 mt-lg-0">
         <div class="sticky-top" style="top: 100px;">
            <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
               <div class="bg-primary py-4 text-center">
                  <h5 class="text-white mb-0 font-weight-bold">Schedule Maintenance</h5>
               </div>
               <div class="card-body p-4">
                  <form action="" method="post" class="premium-form">
                     <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                     <div class="form-group mb-4">
                        <label class="small font-weight-bold text-muted uppercase">Vehicle Model</label>
                        <input type="text" name="v_name" class="form-control bg-light border-0"
                           placeholder="e.g. Swift, Fortuner" required>
                     </div>

                     <div class="form-group mb-4">
                        <label class="small font-weight-bold text-muted uppercase">Service Type</label>
                        <select name="s_name" class="form-control bg-light border-0" required>
                           <option value="" disabled selected>Select Service</option>
                           <option value="General Service">General Service</option>
                           <option value="Engine Repair">Engine Repair</option>
                           <option value="Body Work & Paint">Body Work & Paint</option>
                           <option value="AC Maintenance">AC Maintenance</option>
                           <option value="Water Wash & Spa">Water Wash & Spa</option>
                        </select>
                     </div>

                     <div class="form-group mb-4">
                        <label class="small font-weight-bold text-muted uppercase">Preferred Date</label>
                        <input type="date" name="date" class="form-control bg-light border-0"
                           min="<?php echo date('Y-m-d'); ?>" required>
                     </div>

                     <div class="form-group mb-4">
                        <label class="small font-weight-bold text-muted uppercase">Issue Description</label>
                        <textarea name="rev" class="form-control bg-light border-0" rows="3"
                           placeholder="Tell us what's wrong..."></textarea>
                     </div>

                     <button type="submit" name="submit"
                        class="btn btn-primary btn-block py-3 font-weight-bold rounded-pill shadow-sm">
                        SUBMIT REQUEST
                     </button>
                  </form>
                  <div class="text-center mt-3 pt-3 border-top">
                     <p class="extra-small text-muted mb-0"><i class="fas fa-lock mr-1"></i> Data protected by CAR2GO
                        security</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<style>
   .rounded-xl {
      border-radius: 1.5rem;
   }

   .bg-primary-light {
      background: #e0f2ff;
   }

   .uppercase {
      text-transform: uppercase;
      letter-spacing: 1px;
   }

   .leading-relaxed {
      line-height: 1.8;
   }
</style>

<?php include 'templates/footer.php'; ?>