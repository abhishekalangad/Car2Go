<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();
$l_id = $_SESSION['l_id'];

// Fetch Service Bookings
$query = "SELECT s.s_name, s.s_address, s.s_phone, s.s_image1 as s_image,
                 b.cs_ureview, b.cs_date, b.cs_ereview, b.cs_edate, b.cs_cid, b.sb_id
          FROM bservice b 
          JOIN service_reg s ON b.cs_cid = s.sl_id 
          WHERE b.cs_uid = ? 
          ORDER BY b.sb_id DESC";
// Note: Assuming 'sb_id' is primary key of bservice (guessed from naming convention). 
// If not, sorting by cs_date is safer. Using cs_date DESC.
$query = "SELECT s.s_name, s.s_address, s.s_phone, s.s_image1 as s_image,
                 b.cs_ureview, b.cs_date, b.cs_ereview, b.cs_edate, b.cs_cid
          FROM bservice b 
          JOIN service_reg s ON b.cs_cid = s.sl_id 
          WHERE b.cs_uid = ? 
          ORDER BY b.cs_date DESC";

$bookings = db_fetch_all($con, $query, "i", [$l_id]);

$page_title = 'My Service History - CAR2GO';
include 'templates/header.php';
?>

<div class="page-hero">
   <div class="container hero-content text-center">
      <h1 class="font-weight-bold mb-2 animate__animated animate__fadeInDown">Service <span
            class="text-primary">History</span></h1>
      <p class="lead text-white-50 animate__animated animate__fadeInUp">Track maintenance and repairs for your vehicles.
      </p>
   </div>
</div>

<div class="container animate__animated animate__fadeInUp animate__delay-1s"
   style="margin-top: -80px; position: relative; z-index: 10;">

   <?php if (empty($bookings)): ?>
      <div class="card border-0 shadow-lg rounded-xl p-5 text-center">
         <div class="mb-4"><i class="fas fa-tools fa-4x text-muted opacity-2"></i></div>
         <h3 class="text-muted font-weight-bold">No Service Records</h3>
         <p class="text-muted mb-4">Book a service to keep your vehicle in top condition.</p>
         <a href="viewservicee1.php" class="btn btn-primary rounded-pill px-4">Find Service Center</a>
      </div>
   <?php else: ?>
      <div class="row">
         <?php foreach ($bookings as $row): ?>
            <div class="col-lg-4 col-md-6 mb-4">
               <div class="card border-0 shadow-sm h-100 booking-card trans-hover">
                  <div class="card-body p-4">
                     <div class="d-flex align-items-center mb-4">
                        <div
                           class="service-thumb mr-3 shadow-sm rounded-circle overflow-hidden bg-light d-flex align-items-center justify-content-center"
                           style="width:60px; height:60px;">
                           <?php if (!empty($row['s_image'])): ?>
                              <img src="uploads/services/<?php echo htmlspecialchars($row['s_image']); ?>" class="w-100 h-100"
                                 style="object-fit:cover" onerror="this.src='images/service_icon.png'">
                           <?php else: ?>
                              <i class="fas fa-wrench text-muted fa-lg"></i>
                           <?php endif; ?>
                        </div>
                        <div>
                           <h5 class="font-weight-bold mb-0 text-dark"><?php echo htmlspecialchars($row['s_name']); ?></h5>
                           <div class="small text-muted"><i class="fas fa-map-marker-alt mr-1"></i>
                              <?php echo htmlspecialchars($row['s_address']); ?></div>
                        </div>
                     </div>

                     <div class="bg-light p-3 rounded mb-3">
                        <label class="small text-uppercase font-weight-bold text-muted mb-1">Issue Reported</label>
                        <p class="small text-dark font-weight-bold mb-0"><?php echo htmlspecialchars($row['cs_ureview']); ?>
                        </p>
                        <small class="text-muted d-block mt-1"><i class="far fa-clock mr-1"></i>
                           <?php echo date('M d, Y', strtotime($row['cs_date'])); ?></small>
                     </div>

                     <?php if (!empty($row['cs_ereview'])): ?>
                        <div class="bg-white border rounded p-3 mb-3">
                           <label class="small text-uppercase font-weight-bold text-success mb-1">Center Response</label>
                           <p class="small text-dark mb-0"><?php echo htmlspecialchars($row['cs_ereview']); ?></p>
                           <small class="text-muted d-block mt-1"><i class="fas fa-check-circle mr-1 text-success"></i>
                              <?php echo date('M d, Y', strtotime($row['cs_edate'])); ?></small>
                        </div>
                     <?php else: ?>
                        <div class="alert alert-warning small mb-3">
                           <i class="fas fa-hourglass-half mr-1"></i> Waiting for update...
                        </div>
                     <?php endif; ?>

                     <div class="actions mt-3">
                        <a href="ratings.php?sl_id=<?php echo $row['cs_cid']; ?>"
                           class="btn btn-outline-primary btn-block rounded-pill text-uppercase font-weight-bold text-xs shadow-sm">
                           <i class="far fa-star mr-1"></i> Rate Service
                        </a>
                     </div>
                  </div>
               </div>
            </div>
         <?php endforeach; ?>
      </div>
   <?php endif; ?>
</div>

<style>
   .page-hero {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      padding: 5rem 0 10rem;
      color: white;
      position: relative;
      overflow: hidden;
   }

   .trans-hover {
      transition: transform 0.3s, box-shadow 0.3s;
   }

   .trans-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
   }
</style>

<?php include 'templates/footer.php'; ?>