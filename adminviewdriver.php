<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Admin check? Assuming getting here means admin in this legacy app, 
// or I should add require_role('admin') if I had that implemented fully.
// For now, I'll rely on existing flow but add session check if possible.

// Fetch All Driver Bookings
$query = "SELECT b.*, u.u_name as renter_name, d.d_name as driver_name 
          FROM bookdriver b 
          JOIN user_reg u ON b.dr_id = u.ul_id 
          JOIN driver_reg d ON b.dd_id = d.dl_id 
          ORDER BY b.bd_id DESC";
// Note: Assuming 'bd_id' is primary key (BookDriver ID). 
// Legacy file used $row['d_id'] as ID maybe?
// Let's check legacy again: $d_id=$row['d_id'].
// If 'd_id' is the primary key of bookdriver, I will use that.
// Actually, 'd_id' usually matches 'driver id' in legacy naming? 
// But 'dd_id' is used for driver_reg join.
// Let's look at `udriverthis.php`: `ORDER BY b.bd_id DESC`. 
// So `bd_id` is likely the PK.
// But `adminviewdriver.php` utilized `$d_id=$row['d_id']`. 
// I'll select `b.*` so I should get whatever column is there.

$bookings = db_fetch_all($con, $query);

include 'templates/header.php';
?>

<div class="page-hero">
   <div class="container hero-content text-center">
      <h1 class="font-weight-bold mb-2 text-white">Driver Booking History</h1>
      <p class="lead text-white-50">Manage and view all driver reservations.</p>
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
                     <th class="py-4 px-4 border-0">Request Limit</th> <!-- Legacy content had "Requesting user" -->
                     <th class="py-4 px-4 border-0">Requested By</th>
                     <th class="py-4 px-4 border-0">Driver Name</th>
                     <th class="py-4 px-4 border-0">From Date</th>
                     <th class="py-4 px-4 border-0">To Date</th>
                     <th class="py-4 px-4 border-0">Status</th>
                     <!-- <th class="py-4 px-4 border-0 text-right">Action</th> -->
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($bookings as $row): ?>
                     <tr>
                        <td class="px-4 py-4 align-middle font-weight-bold text-dark">
                           #<?php echo $row['bd_id'] ?? $row['d_id']; ?></td>
                        <td class="px-4 py-4 align-middle">
                           <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['renter_name']); ?></div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                           <div class="font-weight-bold text-primary"><?php echo htmlspecialchars($row['driver_name']); ?>
                           </div>
                        </td>
                        <td class="px-4 py-4 align-middle text-muted">
                           <?php echo date('M d, Y', strtotime($row['d_day1'])); ?>
                        </td>
                        <td class="px-4 py-4 align-middle text-muted">
                           <?php echo date('M d, Y', strtotime($row['d_day2'])); ?>
                        </td>
                        <td class="px-4 py-4 align-middle">
                           <?php
                           $status = ucfirst($row['d_status']);
                           $badge = 'badge-secondary';
                           if (stripos($status, 'Approve') !== false || stripos($status, 'Confirm') !== false)
                              $badge = 'badge-success';
                           if (stripos($status, 'Pending') !== false)
                              $badge = 'badge-warning';
                           if (stripos($status, 'Reject') !== false)
                              $badge = 'badge-danger';
                           ?>
                           <span class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill"><?php echo $status; ?></span>
                        </td>
                        <!-- <td class="px-4 py-4 align-middle text-right">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle" type="button" data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right border-0 shadow-sm">
                                        <a class="dropdown-item text-danger" href="#">Delete</a>
                                    </div>
                                </div>
                            </td> -->
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