<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();

$l_id = $_SESSION['l_id'];

// Fetch user data
$query = "SELECT * FROM user_reg WHERE ul_id = ?";
$user = db_fetch_one($con, $query, "i", [$l_id]);

if (!$user) {
   redirect_with_message('index.php', 'Profile not found.', 'danger');
}

$page_title = 'My Profile - CAR2GO';
include 'templates/header.php';
?>

<style>
   .profile-hero {
      background: linear-gradient(135deg, var(--bg-dark) 0%, #1e293b 100%);
      padding: 80px 0;
      margin-top: -20px;
      color: white;
   }

   .profile-avatar {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      border: 5px solid rgba(255, 255, 255, 0.1);
      background: var(--primary-color);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 4rem;
      font-weight: 700;
      margin-bottom: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
   }

   .profile-card {
      background: white;
      border-radius: 24px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
      margin-top: -60px;
      overflow: hidden;
      border: 1px solid #f1f5f9;
   }

   .info-group {
      padding: 25px;
      border-bottom: 1px solid #f1f5f9;
      transition: background 0.2s ease;
   }

   .info-group:last-child {
      border-bottom: none;
   }

   .info-group:hover {
      background: #f8fafc;
   }

   .info-label {
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      color: #94a3b8;
      letter-spacing: 1px;
      margin-bottom: 5px;
   }

   .info-value {
      font-size: 1.1rem;
      color: var(--bg-dark);
      font-weight: 500;
   }

   .badge-pill {
      font-weight: 600;
      padding: 6px 16px;
   }
</style>

<div class="profile-hero">
   <div class="container text-center">
      <div class="d-flex flex-column align-items-center">
         <div class="profile-avatar">
            <?php echo strtoupper(substr($user['u_name'], 0, 1)); ?>
         </div>
         <h1 class="display-4 font-weight-bold mb-2"><?php echo e($user['u_name']); ?></h1>
         <div class="d-flex gap-3 align-items-center justify-content-center">
            <span class="badge badge-primary badge-pill">Verified Member</span>
            <span class="text-white-50">|</span>
            <span class="text-white-50"><i class="fas fa-calendar-alt mr-1"></i> Member since 2024</span>
         </div>
      </div>
   </div>
</div>

<div class="container pb-5">
   <div class="row justify-content-center">
      <div class="col-lg-8">
         <div class="profile-card">
            <div class="row no-gutters">
               <div class="col-md-6 border-right">
                  <div class="info-group">
                     <div class="info-label">Full Name</div>
                     <div class="info-value"><?php echo e($user['u_name']); ?></div>
                  </div>
                  <div class="info-group">
                     <div class="info-label">Email Address</div>
                     <div class="info-value"><?php echo e($user['u_email']); ?></div>
                  </div>
                  <div class="info-group">
                     <div class="info-label">Phone Number</div>
                     <div class="info-value"><?php echo e($user['u_phone']); ?></div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="info-group">
                     <div class="info-label">Residential Address</div>
                     <div class="info-value"><?php echo e($user['u_address']); ?></div>
                  </div>
                  <div class="info-group">
                     <div class="info-label">Pincode</div>
                     <div class="info-value"><?php echo e($user['u_pincode']); ?></div>
                  </div>
                  <div class="info-group">
                     <div class="info-label">Identity Proof (ID)</div>
                     <div class="info-value">
                        <a href="images/<?php echo e($user['u_licence']); ?>" target="_blank"
                           class="text-primary font-weight-bold">
                           <i class="fas fa-file-pdf mr-1"></i> View Document
                        </a>
                     </div>
                  </div>
               </div>
            </div>

            <div class="bg-light p-4 d-flex justify-content-between align-items-center">
               <div>
                  <a href="uedit.php" class="btn btn-outline-primary px-4 font-weight-bold">
                     <i class="fas fa-edit mr-2"></i> Edit Profile
                  </a>
               </div>
               <div>
                  <button type="button" class="btn btn-link text-danger font-weight-bold" data-toggle="modal"
                     data-target="#deleteModal">
                     <i class="fas fa-trash-alt mr-2"></i> Deactivate Account
                  </button>
               </div>
            </div>
         </div>

         <!-- Dashboard Shortcuts -->
         <div class="row mt-5">
            <div class="col-md-4 mb-4">
               <div class="bg-white p-4 rounded-lg shadow-sm text-center border h-100 transition-hover">
                  <div class="text-primary mb-3"><i class="fas fa-car fa-2x"></i></div>
                  <h6 class="font-weight-bold">My Bookings</h6>
                  <p class="small text-muted">View and manage your car rentals.</p>
                  <a href="user/bookings.php" class="stretched-link"></a>
               </div>
            </div>
            <div class="col-md-4 mb-4">
               <div class="bg-white p-4 rounded-lg shadow-sm text-center border h-100 transition-hover">
                  <div class="text-success mb-3"><i class="fas fa-plus-circle fa-2x"></i></div>
                  <h6 class="font-weight-bold">List My Car</h6>
                  <p class="small text-muted">Earn money by renting your car.</p>
                  <a href="rentingform.php" class="stretched-link"></a>
               </div>
            </div>
            <div class="col-md-4 mb-4">
               <div class="bg-white p-4 rounded-lg shadow-sm text-center border h-100 transition-hover">
                  <div class="text-info mb-3"><i class="fas fa-star fa-2x"></i></div>
                  <h6 class="font-weight-bold">Help & Support</h6>
                  <p class="small text-muted">Get assistance with your account.</p>
                  <a href="contact.php" class="stretched-link"></a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow">
         <div class="modal-header bg-danger text-white">
            <h5 class="modal-title font-weight-bold">Delete Account?</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body p-4 text-center">
            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
            <p class="lead font-weight-bold">This action is permanent.</p>
            <p>Are you sure you want to deactivate your CAR2GO account? All your history and active listings will be
               hidden.</p>
         </div>
         <div class="modal-footer border-0 p-4">
            <button type="button" class="btn btn-light px-4" data-dismiss="modal">Cancel</button>
            <a href="udelete.php" class="btn btn-danger px-4">Yes, Delete Account</a>
         </div>
      </div>
   </div>
</div>

<style>
   .transition-hover {
      transition: all 0.3s ease;
   }

   .transition-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
      border-color: var(--primary-color) !important;
   }
</style>

<?php include 'templates/footer.php'; ?>