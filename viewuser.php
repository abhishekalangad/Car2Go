<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Fetch All Registered Users
$query = "SELECT u.*, l.l_approve 
          FROM login l 
          JOIN user_reg u ON l.l_id = u.ul_id";

$users = db_fetch_all($con, $query);

include 'templates/header.php';
?>

<div class="page-hero">
   <div class="container hero-content text-center">
      <h1 class="font-weight-bold mb-2 text-white">Manage Users</h1>
      <p class="lead text-white-50">Oversee customer accounts and permissions.</p>
   </div>
</div>

<div class="container-fluid py-5" style="background: #f8fafc; min-height: 80vh;">
   <div class="container animate__animated animate__fadeInUp bg-white rounded-lg shadow-sm p-0 overflow-hidden"
      style="margin-top: -80px; position: relative; z-index: 10;">

      <?php if (empty($users)): ?>
         <div class="text-center p-5">
            <h3 class="text-muted font-weight-bold">No Users Registered</h3>
         </div>
      <?php else: ?>
         <div class="table-responsive">
            <table class="table table-hover mb-0">
               <thead class="bg-light text-uppercase small text-muted font-weight-bold">
                  <tr>
                     <th class="py-4 px-4 border-0">User</th>
                     <th class="py-4 px-4 border-0">Contact</th>
                     <th class="py-4 px-4 border-0">License / ID</th>
                     <th class="py-4 px-4 border-0">Status</th>
                     <th class="py-4 px-4 border-0 text-right">Actions</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($users as $row): ?>
                     <tr>
                        <td class="px-4 py-4 align-middle">
                           <div class="d-flex align-items-center">
                              <div
                                 class="avatar-sm rounded-circle mr-3 overflow-hidden bg-light border d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px;">
                                 <!-- Assuming u_image might exist or use initial -->
                                 <span
                                    class="text-primary font-weight-bold"><?php echo strtoupper(substr($row['u_name'], 0, 1)); ?></span>
                              </div>
                              <div>
                                 <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['u_name']); ?></div>
                                 <div class="small text-muted">ID: <?php echo $row['ul_id']; ?></div>
                              </div>
                           </div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                           <div class="small text-dark font-weight-bold"><?php echo htmlspecialchars($row['u_email']); ?>
                           </div>
                           <div class="small text-muted"><?php echo htmlspecialchars($row['u_phone']); ?></div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                           <?php if (!empty($row['u_licence'])): ?>
                              <a href="images/<?php echo htmlspecialchars($row['u_licence']); ?>" target="_blank"
                                 class="btn btn-xs btn-outline-info rounded-pill">View ID</a>
                           <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 align-middle">
                           <?php
                           $status = strtolower($row['l_approve']);
                           $badge = 'badge-secondary';
                           if ($status == 'approve')
                              $badge = 'badge-success';
                           if ($status == 'reject' || $status == 'disapprove')
                              $badge = 'badge-danger';
                           if ($status == 'pending' || $status == '')
                              $badge = 'badge-warning';
                           ?>
                           <span
                              class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill"><?php echo ucfirst($status ?: 'Pending'); ?></span>
                        </td>
                        <td class="px-4 py-4 align-middle text-right">
                           <?php if (strtolower($row['l_approve']) == 'approve'): ?>
                              <a href="disser2.php?sl_id=<?php echo $row['ul_id']; ?>"
                                 class="btn btn-sm btn-outline-warning rounded-pill font-weight-bold shadow-sm"
                                 title="Disapprove">
                                 Suspend
                              </a>
                           <?php else: ?>
                              <a href="appser2.php?sl_id=<?php echo $row['ul_id']; ?>"
                                 class="btn btn-sm btn-success rounded-pill font-weight-bold shadow-sm" title="Approve">
                                 Approve
                              </a>
                           <?php endif; ?>
                           <a href="deleteser2.php?sl_id=<?php echo $row['ul_id']; ?>"
                              class="btn btn-sm btn-light text-danger rounded-circle ml-2" title="Delete"
                              onclick="return confirm('Are you sure you want to delete this user?');">
                              <i class="fas fa-trash"></i>
                           </a>
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