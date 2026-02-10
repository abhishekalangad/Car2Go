<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();
$l_id = $_SESSION['l_id'];

// My Rentals (Outgoing Requests)
// Hypothesis: bookcar.bo_id = Borrower ID (User). bookcar.br_id = Rent Listing ID.
$query = "SELECT r.r_company, r.r_mname, r.r_car, r.r_image, r.rent_amt, 
                 b.b_id, b.b_day1, b.b_day2, b.b_status, b.payment, b.br_id
          FROM bookcar b 
          JOIN rent r ON b.br_id = r.r_id 
          WHERE b.bo_id = ? 
          ORDER BY b.b_id DESC";

// Note: r_image column name? 'r_car' seems to be the image column in 'rent' table based on `rentingform.php` (r_car is filename).
// Let's verify rentingform.php: yes, `uploaded_filenames['car']` -> `r_car` column.

$bookings = db_fetch_all($con, $query, "i", [$l_id]);

$page_title = 'My Car Rentals - CAR2GO';
include 'templates/header.php';
?>

<div class="page-hero">
   <div class="container hero-content text-center">
      <h1 class="font-weight-bold mb-2 animate__animated animate__fadeInDown">My <span
            class="text-primary">Rentals</span></h1>
      <p class="lead text-white-50 animate__animated animate__fadeInUp">Track your vehicle reservation status.</p>
   </div>
</div>

<div class="container animate__animated animate__fadeInUp animate__delay-1s"
   style="margin-top: -80px; position: relative; z-index: 10;">

   <?php if (empty($bookings)): ?>
      <div class="card border-0 shadow-lg rounded-xl p-5 text-center">
         <div class="mb-4"><i class="fas fa-car fa-4x text-muted opacity-2"></i></div>
         <h3 class="text-muted font-weight-bold">No Rentals Yet</h3>
         <p class="text-muted mb-4">Start your journey by finding the perfect car.</p>
         <a href="viewcars.php" class="btn btn-primary rounded-pill px-4">Browse Cars</a>
      </div>
   <?php else: ?>
      <div class="row">
         <?php foreach ($bookings as $row):
            $start = new DateTime($row['b_day1']);
            $end = new DateTime($row['b_day2']);
            $days = $start->diff($end)->days;
            $days = max(1, $days);
            $total = $row['rent_amt'] * $days;
            $car_name = $row['r_company'] . ' ' . $row['r_mname'];
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
               <div class="card border-0 shadow-sm h-100 booking-card trans-hover">
                  <div class="card-body p-4">
                     <div class="d-flex align-items-center mb-4">
                        <div class="car-thumb mr-3 shadow-sm">
                           <?php if (!empty($row['r_car'])): ?>
                              <img src="uploads/cars/<?php echo htmlspecialchars($row['r_car']); ?>" class="w-100 h-100 rounded"
                                 style="object-fit:cover" onerror="this.src='images/car_icon.jpg'">
                           <?php else: ?>
                              <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted"><i
                                    class="fas fa-car"></i></div>
                           <?php endif; ?>
                        </div>
                        <div>
                           <h5 class="font-weight-bold mb-0 text-dark"><?php echo htmlspecialchars($car_name); ?></h5>
                           <div class="small text-muted">Daily: ₹<?php echo number_format($row['rent_amt']); ?></div>
                        </div>
                     </div>

                     <div class="info-row d-flex justify-content-between mb-2">
                        <span class="text-muted small">Dates</span>
                        <span class="font-weight-bold small text-dark">
                           <?php echo date('M d', strtotime($row['b_day1'])); ?> -
                           <?php echo date('M d', strtotime($row['b_day2'])); ?>
                        </span>
                     </div>
                     <div class="info-row d-flex justify-content-between mb-2">
                        <span class="text-muted small">Duration</span>
                        <span class="font-weight-bold small text-dark"><?php echo $days; ?> Days</span>
                     </div>
                     <div class="info-row d-flex justify-content-between mb-3">
                        <span class="text-muted small">Total Cost</span>
                        <span class="font-weight-bold text-primary">₹<?php echo number_format($total); ?></span>
                     </div>

                     <div class="status-badge mb-4 text-center">
                        <?php
                        $statusClass = 'badge-light text-muted';
                        $statusText = ucfirst($row['b_status']);
                        if ($row['b_status'] == 'Approved' || $row['b_status'] == 'confirmed') {
                           $statusClass = 'badge-success-soft text-success';
                           $statusText = 'Confirmed';
                        }
                        if ($row['b_status'] == 'Pending')
                           $statusClass = 'badge-warning-soft text-warning';
                        if ($row['b_status'] == 'Rejected' || $row['b_status'] == 'not approve') {
                           $statusClass = 'badge-danger-soft text-danger';
                           $statusText = $row['b_status'] == 'not approve' ? 'Pending Approval' : 'Rejected';
                        }
                        ?>
                        <span
                           class="badge <?php echo $statusClass; ?> px-3 py-2 rounded-pill"><?php echo $statusText; ?></span>
                     </div>

                     <div class="actions">
                        <?php if ($row['b_status'] == 'confirmed'): ?>
                           <?php if (empty($row['payment']) || $row['payment'] == 'Pending'): ?>
                              <a href="pay.php?id=<?php echo $row['b_id']; ?>"
                                 class="btn btn-success btn-block rounded-pill font-weight-bold shadow-sm">
                                 PAY ₹<?php echo $total; ?>
                              </a>
                           <?php else: ?>
                              <button class="btn btn-light btn-block rounded-pill text-success font-weight-bold" disabled>
                                 <i class="fas fa-check-circle mr-1"></i> PAID
                              </button>
                              <a href="rating.php?r_id=<?php echo $row['br_id']; ?>"
                                 class="btn btn-outline-primary btn-block rounded-pill mt-2 small">
                                 Rate Vehicle
                              </a>
                           <?php endif; ?>
                        <?php elseif ($row['b_status'] == 'Rejected'): ?>
                           <button class="btn btn-light btn-block rounded-pill text-danger disabled">Declined</button>
                        <?php else: ?>
                           <button class="btn btn-light btn-block rounded-pill text-muted disabled">Waiting for
                              Confirmation</button>
                        <?php endif; ?>
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

   .car-thumb {
      width: 80px;
      height: 60px;
      border-radius: 8px;
      overflow: hidden;
      flex-shrink: 0;
   }

   .trans-hover {
      transition: transform 0.3s, box-shadow 0.3s;
   }

   .trans-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
   }

   .badge-success-soft {
      background: #dcfce7;
   }

   .badge-warning-soft {
      background: #fef9c3;
   }

   .badge-danger-soft {
      background: #fee2e2;
   }
</style>

<?php include 'templates/footer.php'; ?>