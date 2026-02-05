<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Fetch all approved drivers
$query = "SELECT d.*, l.l_approve FROM driver_reg d JOIN login l ON d.dl_id = l.l_id WHERE l.l_approve = 'approve'";
$drivers = db_fetch_all($con, $query);

$page_title = 'Professional Drivers - CAR2GO';
include 'templates/header.php';
?>

<div class="hero-section"
   style="height: 350px; display: flex; align-items: center; background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/bg4.jpg'); background-size: cover; background-position: center;">
   <div class="container text-center text-white">
      <h1 class="display-4 font-weight-bold">Hand-Picked <span>Drivers</span></h1>
      <p class="lead">Experienced professionals at your service for a safe and comfortable journey.</p>
   </div>
</div>

<div class="container py-5">
   <div class="row">
      <?php foreach ($drivers as $driver): ?>
         <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm rounded-xl overflow-hidden transition-hover">
               <div class="position-relative">
                  <img src="uploads/drivers/<?php echo e($driver['d_proof']); ?>" class="card-img-top"
                     style="height: 250px; object-fit: cover;" alt="<?php echo e($driver['d_name']); ?>">
                  <div class="driver-price-tag">₹<?php echo e($driver['d_amount']); ?><span>/day</span></div>
               </div>
               <div class="card-body p-4">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                     <h4 class="font-weight-bold mb-0"><?php echo e($driver['d_name']); ?></h4>
                     <div class="text-warning">
                        <i class="fas fa-star"></i> 4.8
                     </div>
                  </div>
                  <div class="small text-muted mb-3">
                     <div class="mb-1"><i class="fas fa-map-marker-alt mr-2"></i> <?php echo e($driver['d_address']); ?>
                     </div>
                     <div><i class="fas fa-id-card mr-2 text-primary"></i> Verified Professional</div>
                  </div>
                  <a href="viewdriverprofile.php?dl_id=<?php echo $driver['dl_id']; ?>"
                     class="btn btn-primary btn-block rounded-pill py-2 font-weight-bold">
                     HIRE DRIVER
                  </a>
               </div>
            </div>
         </div>
      <?php endforeach; ?>

      <?php if (empty($drivers)): ?>
         <div class="col-12 text-center py-5">
            <div class="mb-4"><i class="fas fa-user-slash fa-4x text-muted opacity-3"></i></div>
            <h3 class="text-muted">No drivers available at the moment.</h3>
            <p>Please check back later or try a different location.</p>
         </div>
      <?php endif; ?>
   </div>
</div>

<style>
   .rounded-xl {
      border-radius: 1.5rem;
   }

   .driver-price-tag {
      position: absolute;
      bottom: 15px;
      left: 15px;
      background: var(--primary-color);
      color: white;
      padding: 5px 15px;
      border-radius: 30px;
      font-weight: 700;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
   }

   .driver-price-tag span {
      font-size: 0.7rem;
      font-weight: 400;
      opacity: 0.8;
   }

   .transition-hover:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
   }
</style>

<?php include 'templates/footer.php'; ?>