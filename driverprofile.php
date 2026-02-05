<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();
require_role('driver');

$l_id = $_SESSION['l_id'];

// Fetch driver data
$query = "SELECT d.*, l.l_approve FROM driver_reg d JOIN login l ON d.dl_id = l.l_id WHERE d.dl_id = ?";
$driver = db_fetch_one($con, $query, "i", [$l_id]);

if (!$driver) {
   redirect_with_message('index.php', 'Driver profile not found.', 'danger');
}

$page_title = 'Driver Dashboard - CAR2GO';
include 'templates/header.php';
?>

<style>
   .profile-hero {
      background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
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
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
   }

   .status-badge {
      position: absolute;
      bottom: 5px;
      right: 5px;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      border: 3px solid #0f172a;
      background: #22c55e;
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
            <img src="uploads/drivers/<?php echo e($driver['d_proof']); ?>" class="profile-avatar" alt="Driver Photo">
            <div class="status-badge" title="Active"></div>
         </div>
         <h1 class="display-4 font-weight-bold mb-2"><?php echo e($driver['d_name']); ?></h1>
         <div class="d-flex gap-2 align-items-center justify-content-center">
            <?php if ($driver['l_approve'] === 'approve'): ?>
               <span class="badge badge-success px-3 py-2 rounded-pill"><i class="fas fa-check-circle mr-1"></i> Verified
                  Partner</span>
            <?php else: ?>
               <span class="badge badge-warning px-3 py-2 rounded-pill"><i class="fas fa-clock mr-1"></i> Under
                  Review</span>
            <?php endif; ?>
            <span class="badge badge-outline-light border px-3 py-2 rounded-pill">Driver ID:
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
                  <div class="stat-value">₹<?php echo e($driver['d_amount']); ?></div>
                  <div class="stat-label">Daily Charge</div>
               </div>
               <div class="col-md-4 stat-box">
                  <div class="stat-value">
                     <?php
                     $rating_q = "SELECT AVG(rating) as avg FROM ratingd WHERE l_id = ?";
                     $rating = db_fetch_one($con, $rating_q, "i", [$l_id])['avg'] ?? 0;
                     echo number_format($rating, 1);
                     ?> <i class="fas fa-star text-warning small"></i>
                  </div>
                  <div class="stat-label">Average Rating</div>
               </div>
               <div class="col-md-4 stat-box">
                  <div class="stat-value">
                     <?php
                     $booked_q = "SELECT COUNT(*) as count FROM bookcar WHERE br_id = ? AND b_status = 'Booked'";
                     echo db_fetch_one($con, $booked_q, "i", [$l_id])['count'];
                     ?>
                  </div>
                  <div class="stat-label">Active Gigs</div>
               </div>
            </div>

            <div class="p-5">
               <div class="row">
                  <div class="col-md-6 mb-4">
                     <h6 class="font-weight-bold text-muted text-uppercase small mb-3">Contact Information</h6>
                     <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary-light text-primary rounded-circle p-2 mr-3"><i
                              class="fas fa-envelope"></i></div>
                        <div><?php echo e($driver['d_email']); ?></div>
                     </div>
                     <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary-light text-primary rounded-circle p-2 mr-3"><i class="fas fa-phone"></i>
                        </div>
                        <div><?php echo e($driver['d_phone']); ?></div>
                     </div>
                     <div class="d-flex align-items-center">
                        <div class="bg-primary-light text-primary rounded-circle p-2 mr-3"><i
                              class="fas fa-map-marker-alt"></i></div>
                        <div><?php echo e($driver['d_address']); ?>, <?php echo e($driver['d_pincode']); ?></div>
                     </div>
                  </div>
                  <div class="col-md-6 mb-4">
                     <h6 class="font-weight-bold text-muted text-uppercase small mb-3">Documents & Compliance</h6>
                     <div class="card bg-light border-0 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                           <span><i class="fas fa-id-card mr-2 text-primary"></i> Driving Licence</span>
                           <a href="uploads/documents/<?php echo e($driver['d_licence']); ?>" target="_blank"
                              class="btn btn-sm btn-white border">View</a>
                        </div>
                     </div>
                     <div class="alert alert-info small border-0 shadow-none mb-0">
                        <i class="fas fa-info-circle mr-2"></i> Your documents are visible to customers for trust and
                        verification purposes.
                     </div>
                  </div>
               </div>

               <div class="d-flex gap-2 mt-4 pt-4 border-top">
                  <a href="dedit.php" class="btn btn-primary px-5">Edit Profile</a>
                  <a href="dvfeed.php" class="btn btn-outline-secondary px-5">My Reviews</a>
               </div>
            </div>
         </div>

         <!-- Driver Actions -->
         <div class="row">
            <div class="col-md-6 mb-4">
               <div class="bg-white p-4 rounded-lg shadow-sm d-flex align-items-center transition-hover border">
                  <div class="bg-success-light text-success rounded p-3 mr-4">
                     <i class="fas fa-calendar-check fa-2x"></i>
                  </div>
                  <div>
                     <h5 class="font-weight-bold mb-1">My Bookings</h5>
                     <p class="text-muted small mb-0">Accept or track your riding assignments.</p>
                  </div>
                  <a href="driver/assignments.php" class="stretched-link"></a>
               </div>
            </div>
            <div class="col-md-6 mb-4">
               <div class="bg-white p-4 rounded-lg shadow-sm d-flex align-items-center transition-hover border">
                  <div class="bg-warning-light text-warning rounded p-3 mr-4">
                     <i class="fas fa-wallet fa-2x"></i>
                  </div>
                  <div>
                     <h5 class="font-weight-bold mb-1">Earnings</h5>
                     <p class="text-muted small mb-0">Track your daily income and payouts.</p>
                  </div>
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

   .bg-success-light {
      background: #f0fdf4;
   }

   .bg-warning-light {
      background: #fffbeb;
   }

   .transition-hover:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
      border-color: var(--primary-color) !important;
   }
</style>

<?php include 'templates/footer.php'; ?>