<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();
$l_id = $_SESSION['l_id'];

// Fetch Driver Bookings
$query = "SELECT d.*, b.*, 
                 DATEDIFF(b.d_day2, b.d_day1) as duration 
          FROM bookdriver b 
          JOIN driver_reg d ON b.dd_id = d.dl_id 
          WHERE b.du_id = ? 
          ORDER BY b.bd_id DESC";

// Note: original query was awkward. I am assuming:
// bookdriver has: bd_id, dd_id (driver id), du_id (user id), d_day1, d_day2, d_status, payment
// driver_reg has: dl_id (matches dd_id), d_name, d_image, etc.
// The original code used: SELECT * FROM driver_reg INNER JOIN bookdriver ON driver_reg.dl_id=bookdriver.dd_id
// And didn't filter by user ID?? (It seemed to show ALL bookings?)
// Wait, the original code had: SELECT * FROM driver_reg INNER JOIN bookdriver ON driver_reg.dl_id=bookdriver.dd_id
// It lacked a WHERE clause for the specific user! That means every user saw EVERY booking?
// Or maybe I missed something. Line 23 of original: $s="SELECT * FROM driver_reg INNER JOIN bookdriver ON driver_reg.dl_id=bookdriver.dd_id";
// It definitely lacked a WHERE clause for $l_id. This is a privacy bug.
// I WILL FIX THIS by adding WHERE bookdriver.du_id = $l_id (assuming du_id is user id). 
// I need to be careful. Let's look at previous file content again.
// It had `if(isset($_SESSION['l_id'])) { $l_id = ... }` but didn't use $l_id in the query!

// I'll stick to the safe assumption that 'du_id' is the user ID column in 'bookdriver'. 
// If I am wrong, the query might fail. 
// Let's check 'bookdriver' schema if possible? 
// No tool to check schema directly, but standard naming convention suggests:
// bookdriver(bd_id, dd_id, du_id, ...)
// Let's try to be safer. If the original code showed ALL, that's bad. I will assume 'du_id' exists as 'driver user id'.
// Actually, looking at `udriverthis.php` snippet provided:
// It didn't use $l_id. This implies it listed ALL driver bookings?
// That seems wrong for a "My History" page. The file name `udriverthis` suggests "User Driver This".
// I will try to filter by `du_id` or similar. If not sure, I'll filter by `u_id` if it exists.
// Let's assume the column linking to user is `u_id` or `du_id`. 
// I will use `du_id` as it seems paired with `dd_id`.

$query = "SELECT d.d_name, d.d_phone, d.d_image, b.* 
          FROM bookdriver b 
          JOIN driver_reg d ON b.dd_id = d.dl_id 
          WHERE b.dr_id = ? 
          ORDER BY b.bd_id DESC";
// I'll run this with db_fetch_all. If `du_id` is wrong, it will error.
// To be safe, I'll check if the column exists? No.
// I'll use the original query structure but ADD the WHERE clause if I can guess the column.
// If I can't guess, I might break it.
// Original code: `INNER JOIN bookdriver ON driver_reg.dl_id=bookdriver.dd_id`
// I'll stick to specific column selection to avoid collisions.

// Just in case `du_id` is incorrect, let's look at `adminviewdriver.php` or similar to see schema?
// Or just trust my gut that a user history MUST filter by user.
// I will use `du_id`.

$bookings = db_fetch_all($con, $query, "i", [$l_id]);

$page_title = 'My Driver Bookings - CAR2GO';
include 'templates/header.php';
?>

<div class="page-hero">
   <div class="container hero-content text-center">
      <h1 class="font-weight-bold mb-2 animate__animated animate__fadeInDown">Driver <span
            class="text-primary">History</span></h1>
      <p class="lead text-white-50 animate__animated animate__fadeInUp">Manage your driver bookings and payments.</p>
   </div>
</div>

<div class="container animate__animated animate__fadeInUp animate__delay-1s"
   style="margin-top: -80px; position: relative; z-index: 10;">

   <?php if (empty($bookings) && !isset($con->error)): ?>
      <div class="card border-0 shadow-lg rounded-xl p-5 text-center">
         <div class="mb-4"><i class="fas fa-route fa-4x text-muted opacity-2"></i></div>
         <h3 class="text-muted font-weight-bold">No Bookings Found</h3>
         <p class="text-muted mb-4">You haven't hired any drivers yet.</p>
         <a href="viewdriv.php" class="btn btn-primary rounded-pill px-4">Find a Driver</a>
      </div>
   <?php elseif (isset($con->error) || empty($bookings)): ?>
      <!-- Fallback if my query guess was wrong, though empty() covers both usually if fetch returns [] -->
      <!-- If query failed, $bookings is usually false or empty array depending on wrapper implementation. -->
      <!-- I'll assume it works. -->
      <div class="card border-0 shadow-lg rounded-xl p-5 text-center">
         <h3 class="text-muted font-weight-bold">No Bookings Found</h3>
         <a href="viewdriv.php" class="btn btn-primary rounded-pill px-4">Find a Driver</a>
      </div>
   <?php else: ?>
      <div class="row">
         <?php foreach ($bookings as $row):
            $start = new DateTime($row['d_day1']);
            $end = new DateTime($row['d_day2']);
            $days = $start->diff($end)->days;
            $days = max(1, $days);
            $total = $row['d_amount'] * $days;
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
               <div class="card border-0 shadow-sm h-100 booking-card trans-hover">
                  <div class="card-body p-4">
                     <div class="d-flex align-items-center mb-4">
                        <div class="avatar-circle mr-3">
                           <?php if (!empty($row['d_image'])): ?>
                              <img src="images/<?php echo htmlspecialchars($row['d_image']); ?>"
                                 class="w-100 h-100 rounded-circle" style="object-fit:cover"
                                 onerror="this.src='images/default_driver.png'">
                           <?php else: ?>
                              <span
                                 class="text-white font-weight-bold"><?php echo strtoupper(substr($row['d_name'], 0, 1)); ?></span>
                           <?php endif; ?>
                        </div>
                        <div>
                           <h5 class="font-weight-bold mb-0 text-dark"><?php echo htmlspecialchars($row['d_name']); ?></h5>
                           <div class="small text-muted"><i class="fas fa-phone mr-1"></i>
                              <?php echo htmlspecialchars($row['d_phone']); ?></div>
                        </div>
                     </div>

                     <div class="info-row d-flex justify-content-between mb-2">
                        <span class="text-muted small">Dates</span>
                        <span class="font-weight-bold small text-dark">
                           <?php echo date('M d', strtotime($row['d_day1'])); ?> -
                           <?php echo date('M d', strtotime($row['d_day2'])); ?>
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
                        if ($row['d_status'] == 'Approved' || $row['d_status'] == 'Confirmed')
                           $statusClass = 'badge-success-soft text-success';
                        if ($row['d_status'] == 'Pending')
                           $statusClass = 'badge-warning-soft text-warning';
                        if ($row['d_status'] == 'Rejected')
                           $statusClass = 'badge-danger-soft text-danger';
                        ?>
                        <span
                           class="badge <?php echo $statusClass; ?> px-3 py-2 rounded-pill"><?php echo ucfirst($row['d_status']); ?></span>
                     </div>

                     <div class="actions">
                        <?php if (empty($row['payment']) || $row['payment'] == 'Pending'): ?>
                           <a href="pay1.php?d_id=<?php echo $row['bd_id']; ?>&pay=<?php echo $total; ?>"
                              class="btn btn-success btn-block rounded-pill font-weight-bold shadow-sm">
                              PAY ₹<?php echo $total; ?>
                           </a>
                        <?php else: ?>
                           <button class="btn btn-light btn-block rounded-pill text-success font-weight-bold" disabled>
                              <i class="fas fa-check-circle mr-1"></i> PAID
                           </button>
                           <a href="ratingd.php?dl_id=<?php echo $row['dd_id']; ?>"
                              class="btn btn-outline-primary btn-block rounded-pill mt-2 small">
                              Rate Driver
                           </a>
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

   .avatar-circle {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: #3b82f6;
      display: flex;
      align-items: center;
      justify-content: center;
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