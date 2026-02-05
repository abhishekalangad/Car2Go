<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();
require_role('service center');

$l_id = $_SESSION['l_id'];

// Fetch service center data
$query = "SELECT s.*, l.l_approve FROM service_reg s JOIN login l ON s.sl_id = l.l_id WHERE s.sl_id = ?";
$service = db_fetch_one($con, $query, "i", [$l_id]);

if (!$service) {
   redirect_with_message('index.php', 'Service Center profile not found.', 'danger');
}

$page_title = 'Service Partner Dashboard - CAR2GO';
include 'templates/header.php';
?>

<style>
   .profile-hero {
      background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);
      padding: 80px 0;
      margin-top: -20px;
      color: white;
   }

   .profile-avatar-container {
      width: 150px;
      height: 150px;
      position: relative;
      margin-bottom: 25px;
   }

   .profile-avatar {
      width: 100%;
      height: 100%;
      border-radius: 20px;
      object-fit: cover;
      border: 5px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      background: white;
   }

   .info-card {
      background: white;
      border-radius: 24px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
      margin-top: -60px;
      overflow: hidden;
      border: 1px solid #f1f5f9;
   }

   .stat-box {
      text-align: center;
      padding: 30px;
      border-right: 1px solid #f1f5f9;
   }

   .stat-box:last-child {
      border-right: none;
   }

   .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--bg-dark);
   }

   .stat-label {
      font-size: 0.75rem;
      text-transform: uppercase;
      color: #64748b;
      letter-spacing: 1px;
   }
</style>

<div class="profile-hero">
   <div class="container text-center">
      <div class="d-flex flex-column align-items-center">
         <div class="profile-avatar-container">
            <img src="uploads/services/<?php echo e($service['s_rc']); ?>" class="profile-avatar"
               alt="Service Center Photo">
         </div>
         <h1 class="display-4 font-weight-bold mb-2"><?php echo e($service['s_name']); ?></h1>
         <div class="d-flex gap-2 align-items-center justify-content-center">
            <?php if ($service['l_approve'] === 'approve'): ?>
               <span class="badge badge-success px-3 py-2 rounded-pill"><i class="fas fa-check-circle mr-1"></i> Authorized
                  Partner</span>
            <?php else: ?>
               <span class="badge badge-warning px-3 py-2 rounded-pill"><i class="fas fa-clock mr-1"></i> Under
                  Verification</span>
            <?php endif; ?>
            <span class="badge badge-outline-light border px-3 py-2 rounded-pill">Partner ID:
               #<?php echo $l_id; ?></span>
         </div>
      </div>
   </div>
</div>

<div class="container pb-5">
   <div class="row justify-content-center">
      <div class="col-lg-10">
         <div class="info-card mb-5">
            <!-- Stats Row -->
            <div class="row no-gutters bg-light border-bottom">
               <div class="col-md-4 stat-box">
                  <div class="stat-value">
                     <?php
                     $services_q = "SELECT COUNT(*) as count FROM serviceform WHERE sl_id = ?";
                     echo db_fetch_one($con, $services_q, "i", [$l_id])['count'] ?? 0;
                     ?>
                  </div>
                  <div class="stat-label">Active Services</div>
               </div>
               <div class="col-md-4 stat-box">
                  <div class="stat-value">
                     <?php
                     $rating_q = "SELECT AVG(rating) as avg FROM ratings WHERE l_id = ?";
                     $rating = db_fetch_one($con, $rating_q, "i", [$l_id])['avg'] ?? 0;
                     echo number_format($rating, 1);
                     ?> <i class="fas fa-star text-warning small"></i>
                  </div>
                  <div class="stat-label">Partner Rating</div>
               </div>
               <div class="col-md-4 stat-box">
                  <div class="stat-value">
                     <?php
                     $req_q = "SELECT COUNT(*) as count FROM servicereq WHERE s_id = ? AND status = 'Pending'";
                     echo db_fetch_one($con, $req_q, "i", [$l_id])['count'] ?? 0;
                     ?>
                  </div>
                  <div class="stat-label">New Requests</div>
               </div>
            </div>

            <div class="p-5">
               <div class="row">
                  <div class="col-md-6 mb-4">
                     <h6 class="font-weight-bold text-muted text-uppercase small mb-3">Facility Details</h6>
                     <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary-light text-primary rounded-circle p-2 mr-3"
                           style="width:35px; height:35px; display:flex; align-items:center; justify-content:center;"><i
                              class="fas fa-envelope"></i></div>
                        <div><?php echo e($service['s_email']); ?></div>
                     </div>
                     <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary-light text-primary rounded-circle p-2 mr-3"
                           style="width:35px; height:35px; display:flex; align-items:center; justify-content:center;"><i
                              class="fas fa-phone"></i></div>
                        <div><?php echo e($service['s_phone']); ?></div>
                     </div>
                     <div class="d-flex align-items-start">
                        <div class="bg-primary-light text-primary rounded-circle p-2 mr-3"
                           style="width:35px; height:35px; min-width:35px; display:flex; align-items:center; justify-content:center;">
                           <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div><?php echo e($service['s_address']); ?>, <?php echo e($service['s_pincode']); ?></div>
                     </div>
                  </div>
                  <div class="col-md-6 mb-4">
                     <h6 class="font-weight-bold text-muted text-uppercase small mb-3">Legal & Registration</h6>
                     <div class="card bg-light border-0 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                           <span><i class="fas fa-file-contract mr-2 text-primary"></i> Trade License / ID</span>
                           <a href="uploads/documents/<?php echo e($service['s_licence']); ?>" target="_blank"
                              class="btn btn-sm btn-white border shadow-sm">View Doc</a>
                        </div>
                     </div>
                     <div class="alert alert-primary small border-0 shadow-sm mb-0"
                        style="background: #e0e7ff; color: #3730a3;">
                        <i class="fas fa-certificate mr-2"></i> Official CAR2GO Service Partner badge active.
                     </div>
                  </div>
               </div>

               <div class="d-flex gap-2 mt-4 pt-4 border-top">
                  <a href="sedit.php" class="btn btn-primary px-5 shadow-sm">Manage Facility</a>
                  <a href="svfeed.php" class="btn btn-outline-secondary px-5">Feedback Loop</a>
               </div>
            </div>
         </div>

         <!-- Service Actions -->
         <div class="row">
            <div class="col-md-4 mb-4">
               <div class="bg-white p-4 rounded-lg shadow-sm text-center transition-hover border h-100">
                  <div class="bg-indigo-light text-indigo rounded-circle p-3 mx-auto mb-3"
                     style="width:60px; height:60px; display:flex; align-items:center; justify-content:center;">
                     <i class="fas fa-plus-circle fa-2x"></i>
                  </div>
                  <h5 class="font-weight-bold">Add Service</h5>
                  <p class="text-muted small">List new maintenance or repair offerings.</p>
                  <a href="serviceform.php" class="stretched-link"></a>
               </div>
            </div>
            <div class="col-md-4 mb-4">
               <div class="bg-white p-4 rounded-lg shadow-sm text-center transition-hover border h-100">
                  <div class="bg-teal-light text-teal rounded-circle p-3 mx-auto mb-3"
                     style="width:60px; height:60px; display:flex; align-items:center; justify-content:center;">
                     <i class="fas fa-tasks fa-2x"></i>
                  </div>
                  <h5 class="font-weight-bold">Manage Tasks</h5>
                  <p class="text-muted small">Update status of ongoing vehicle services.</p>
                  <a href="service/tasks.php" class="stretched-link"></a>
               </div>
            </div>
            <div class="col-md-4 mb-4">
               <div class="bg-white p-4 rounded-lg shadow-sm text-center transition-hover border h-100">
                  <div class="bg-rose-light text-rose rounded-circle p-3 mx-auto mb-3"
                     style="width:60px; height:60px; display:flex; align-items:center; justify-content:center;">
                     <i class="fas fa-history fa-2x"></i>
                  </div>
                  <h5 class="font-weight-bold">History</h5>
                  <p class="text-muted small">View archive of completed service jobs.</p>
                  <a href="#" class="stretched-link"></a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<style>
   .bg-primary-light {
      background: #eff6ff;
   }

   .bg-indigo-light {
      background: #e0e7ff;
      color: #4338ca;
   }

   .bg-teal-light {
      background: #f0fdfa;
      color: #0d9488;
   }

   .bg-rose-light {
      background: #fff1f2;
      color: #e11d48;
   }

   .transition-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
      border-color: var(--primary-color) !important;
   }
</style>

<?php include 'templates/footer.php'; ?>