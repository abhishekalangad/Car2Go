<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Ensure only admin
require_role('admin');

// Fetch All Employees (Assuming employee login type is 'employe' based on employereg.php)
$query = "SELECT e.*, l.l_approve 
          FROM login l 
          JOIN emp_reg e ON l.l_id = e.el_id 
          WHERE l.l_type = 'employe'";

$employees = db_fetch_all($con, $query);

$page_title = 'Manage Employees - CAR2GO';
include 'templates/header.php';
?>

<div class="page-hero">
   <div class="container hero-content text-center">
      <h1 class="font-weight-bold mb-2 text-white">Manage Employees</h1>
      <p class="lead text-white-50">Oversee staff accounts and details.</p>
   </div>
</div>

<div class="container-fluid py-5" style="background: #f8fafc; min-height: 80vh;">
   <div class="container animate__animated animate__fadeInUp bg-white rounded-lg shadow-sm p-0 overflow-hidden"
      style="margin-top: -80px; position: relative; z-index: 10;">

      <?php if (empty($employees)): ?>
         <div class="text-center p-5">
            <h3 class="text-muted font-weight-bold">No Employees Registered</h3>
            <a href="employereg.php" class="btn btn-primary rounded-pill mt-3 px-4">Register Employee</a>
         </div>
      <?php else: ?>
         <div class="table-responsive">
            <table class="table table-hover mb-0">
               <thead class="bg-light text-uppercase small text-muted font-weight-bold">
                  <tr>
                     <th class="py-4 px-4 border-0">Name</th>
                     <th class="py-4 px-4 border-0">Contact Info</th>
                     <th class="py-4 px-4 border-0">Address</th>
                     <th class="py-4 px-4 border-0 text-right">Actions</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($employees as $row): ?>
                     <tr>
                        <td class="px-4 py-4 align-middle">
                           <div class="d-flex align-items-center">
                              <div
                                 class="avatar-sm rounded-circle mr-3 overflow-hidden bg-light border d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px;">
                                 <span
                                    class="text-success font-weight-bold"><?php echo strtoupper(substr($row['e_name'], 0, 1)); ?></span>
                              </div>
                              <div>
                                 <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['e_name']); ?></div>
                                 <div class="small text-muted">ID: <?php echo $row['el_id']; ?></div>
                              </div>
                           </div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                           <div class="small text-dark font-weight-bold"><?php echo htmlspecialchars($row['e_email']); ?>
                           </div>
                           <div class="small text-muted"><?php echo htmlspecialchars($row['e_phone']); ?></div>
                        </td>
                        <td class="px-4 py-4 align-middle text-muted small">
                           <?php echo htmlspecialchars($row['e_address']); ?>, <br>
                           Pin: <?php echo htmlspecialchars($row['e_pincode']); ?>
                        </td>
                        <td class="px-4 py-4 align-middle text-right">
                           <a href="deleteemp.php?sl_id=<?php echo $row['el_id']; ?>"
                              class="btn btn-sm btn-light text-danger rounded-circle ml-2" title="Delete"
                              onclick="return confirm('Are you sure you want to delete this employee?');">
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
</style>

<?php include 'templates/footer.php'; ?>