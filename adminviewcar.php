<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Fetch All Car Bookings
$query = "SELECT b.*, r.r_company, r.r_mname, u.u_name as renter_name, r.rent_amt
          FROM bookcar b 
          JOIN rent r ON b.br_id = r.r_id 
          JOIN user_reg u ON b.bo_id = u.ul_id 
          ORDER BY b.b_id DESC";

$bookings = db_fetch_all($con, $query);

include 'templates/header.php';
?>

<div class="page-hero">
   <div class="container hero-content text-center">
      <h1 class="font-weight-bold mb-2 text-white">Car Booking History</h1>
      <p class="lead text-white-50">Manage all vehicle rental reservations.</p>
   </div>
</div>

<div class="container-fluid py-5" style="background: #f8fafc; min-height: 80vh;">
   <div class="container animate__animated animate__fadeInUp bg-white rounded-lg shadow-sm p-0 overflow-hidden"
      style="margin-top: -80px; position: relative; z-index: 10;">

      <?php if (empty($bookings)): ?>
         <div class="text-center p-5">
            <h3 class="text-muted font-weight-bold">No Bookings Found</h3>
         </div>
      <?php else: ?>
         <div class="table-responsive">
            <table class="table table-hover mb-0">
               <thead class="bg-light text-uppercase small text-muted font-weight-bold">
                  <tr>
                     <th class="py-4 px-4 border-0">ID</th>
                     <th class="py-4 px-4 border-0">Requested By</th>
                     <th class="py-4 px-4 border-0">Vehicle</th>
                     <th class="py-4 px-4 border-0">Dates</th>
                     <th class="py-4 px-4 border-0">Total Amount</th>
                     <th class="py-4 px-4 border-0">Status</th>
                     <th class="py-4 px-4 border-0">Payment</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($bookings as $row):
                     $start = new DateTime($row['b_day1']);
                     $end = new DateTime($row['b_day2']);
                     $days = max(1, $start->diff($end)->days);
                     $total = $days * $row['rent_amt'];
                     ?>
                     <tr>
                        <td class="px-4 py-4 align-middle font-weight-bold text-dark">#<?php echo $row['b_id']; ?></td>
                        <td class="px-4 py-4 align-middle">
                           <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['renter_name']); ?></div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                           <div class="font-weight-bold text-primary">
                              <?php echo htmlspecialchars($row['r_company'] . ' ' . $row['r_mname']); ?>
                           </div>
                        </td>
                        <td class="px-4 py-4 align-middle text-muted small">
                           <?php echo date('M d', strtotime($row['b_day1'])); ?> -
                           <?php echo date('M d', strtotime($row['b_day2'])); ?>
                           <div class="font-weight-bold text-dark"><?php echo $days; ?> Days</div>
                        </td>
                        <td class="px-4 py-4 align-middle font-weight-bold text-dark">
                           ₹<?php echo number_format($total); ?>
                        </td>
                        <td class="px-4 py-4 align-middle">
                           <?php
                           $status = ucfirst($row['b_status']);
                           $badge = 'badge-secondary';
                           if (stripos($status, 'Confirm') !== false)
                              $badge = 'badge-success';
                           if (stripos($status, 'Pending') !== false)
                              $badge = 'badge-warning';
                           if (stripos($status, 'Reject') !== false || stripos($status, 'Not') !== false)
                              $badge = 'badge-danger';
                           ?>
                           <span class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill"><?php echo $status; ?></span>
                        </td>
                        <td class="px-4 py-4 align-middle">
                           <?php if (!empty($row['payment'])): ?>
                              <span class="text-success small font-weight-bold"><i class="fas fa-check-circle mr-1"></i>
                                 Paid</span>
                           <?php else: ?>
                              <span class="text-muted small">Pending</span>
                           <?php endif; ?>
                        </td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      <?php endif; ?>
   </div>
</div>

<style>
   .page-hero {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      padding: 5rem 0 8rem;
      color: white;
      position: relative;
      overflow: hidden;
   }

   .rounded-lg {
      border-radius: 15px;
   }

   .badge-success {
      background-color: #dcfce7;
      color: #166534;
   }

   .badge-warning {
      background-color: #fef9c3;
      color: #854d0e;
   }

   .badge-danger {
      background-color: #fee2e2;
      color: #991b1b;
   }

   .badge-secondary {
      background-color: #f1f5f9;
      color: #475569;
   }
</style>

<?php include 'templates/footer.php'; ?>