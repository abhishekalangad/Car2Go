<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Fetch All Service Bookings
$query = "SELECT b.*, u.u_name as user_name, s.s_name as center_name, s.s_phone
          FROM bservice b 
          JOIN user_reg u ON b.cs_uid = u.ul_id 
          JOIN service_reg s ON b.cs_cid = s.sl_id 
          ORDER BY b.cs_date DESC";

$bookings = db_fetch_all($con, $query);

include 'templates/header.php';
?>

<div class="page-hero">
   <div class="container hero-content text-center">
      <h1 class="font-weight-bold mb-2 text-white">Service Booking History</h1>
      <p class="lead text-white-50">Monitor all service center appointments.</p>
   </div>
</div>

<div class="container-fluid py-5" style="background: #f8fafc; min-height: 80vh;">
   <div class="container animate__animated animate__fadeInUp bg-white rounded-lg shadow-sm p-0 overflow-hidden"
      style="margin-top: -80px; position: relative; z-index: 10;">

      <?php if (empty($bookings)): ?>
         <div class="text-center p-5">
            <h3 class="text-muted font-weight-bold">No Records Found</h3>
         </div>
      <?php else: ?>
         <div class="table-responsive">
            <table class="table table-hover mb-0">
               <thead class="bg-light text-uppercase small text-muted font-weight-bold">
                  <tr>
                     <th class="py-4 px-4 border-0">Requested By</th>
                     <th class="py-4 px-4 border-0">Service Center</th>
                     <th class="py-4 px-4 border-0" style="width: 30%;">Issue / Request</th>
                     <th class="py-4 px-4 border-0">Request Date</th>
                     <th class="py-4 px-4 border-0">Action Taken</th>
                     <th class="py-4 px-4 border-0">Completion Date</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($bookings as $row): ?>
                     <tr>
                        <td class="px-4 py-4 align-middle">
                           <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['user_name']); ?></div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                           <div class="font-weight-bold text-primary"><?php echo htmlspecialchars($row['center_name']); ?>
                           </div>
                           <div class="small text-muted"><?php echo htmlspecialchars($row['s_phone']); ?></div>
                        </td>
                        <td class="px-4 py-4 align-middle text-muted small">
                           "<?php echo htmlspecialchars($row['cs_ureview']); ?>"
                        </td>
                        <td class="px-4 py-4 align-middle text-dark font-weight-bold">
                           <?php echo date('M d, Y', strtotime($row['cs_date'])); ?>
                        </td>
                        <td class="px-4 py-4 align-middle small">
                           <?php if (!empty($row['cs_ereview'])): ?>
                              <span class="text-success"><?php echo htmlspecialchars($row['cs_ereview']); ?></span>
                           <?php else: ?>
                              <span class="badge badge-warning-soft text-warning px-2 py-1 rounded">Pending</span>
                           <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 align-middle text-muted">
                           <?php if (!empty($row['cs_edate'])): ?>
                              <?php echo date('M d, Y', strtotime($row['cs_edate'])); ?>
                           <?php else: ?>
                              -
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

   .badge-warning-soft {
      background: #fef9c3;
   }
</style>

<?php include 'templates/footer.php'; ?>