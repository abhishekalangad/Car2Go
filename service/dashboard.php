<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_login();
require_role('service center');

$l_id = $_SESSION['l_id'];

// Fetch service center data
$query = "SELECT s.*, l.l_approve FROM service_reg s JOIN login l ON s.sl_id = l.l_id WHERE s.sl_id = ?";
$service = db_fetch_one($con, $query, "i", [$l_id]);

if (!$service) {
    redirect_with_message('../index.php', 'Profile not found.', 'danger');
}

$page_title = 'Partner Dashboard - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="profile-hero"
    style="background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%); padding: 60px 0; color: white;">
    <div class="container d-flex align-items-center">
        <div class="bg-white rounded p-1 mr-4 shadow">
            <img src="../uploads/services/<?php echo e($service['s_rc']); ?>"
                style="width: 100px; height: 100px; object-fit: cover; border-radius: 12px;">
        </div>
        <div>
            <h1 class="font-weight-bold mb-1">
                <?php echo e($service['s_name']); ?>
            </h1>
            <p class="mb-0 opacity-8"><i class="fas fa-map-marker-alt mr-2"></i>
                <?php echo e($service['s_address']); ?>
            </p>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm p-4 text-center h-100 transition-hover">
                <div class="text-indigo mb-3"><i class="fas fa-tools fa-2x"></i></div>
                <h6 class="font-weight-bold">Active Services</h6>
                <div class="h4 font-weight-bold">
                    <?php echo db_fetch_one($con, "SELECT COUNT(*) as c FROM serviceform WHERE sl_id = ?", "i", [$l_id])['c']; ?>
                </div>
                <a href="../serviceform.php" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm p-4 text-center h-100 transition-hover">
                <div class="text-teal mb-3"><i class="fas fa-tasks fa-2x"></i></div>
                <h6 class="font-weight-bold">Pending Tasks</h6>
                <div class="h4 font-weight-bold">
                    <?php echo db_fetch_one($con, "SELECT COUNT(*) as c FROM servicereq WHERE s_id = ? AND status='Pending'", "i", [$l_id])['c']; ?>
                </div>
                <a href="tasks.php" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm p-4 text-center h-100 transition-hover">
                <div class="text-warning mb-3"><i class="fas fa-star fa-2x"></i></div>
                <h6 class="font-weight-bold">Partner Rating</h6>
                <div class="h4 font-weight-bold">
                    <?php echo number_format(db_fetch_one($con, "SELECT AVG(rating) as v FROM ratings WHERE l_id=?", "i", [$l_id])['v'] ?? 0, 1); ?>
                </div>
                <a href="../svfeed.php" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm p-4 text-center h-100 transition-hover">
                <div class="text-danger mb-3"><i class="fas fa-cog fa-2x"></i></div>
                <h6 class="font-weight-bold">Settings</h6>
                <p class="small text-muted">Update center details.</p>
                <a href="../sedit.php" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <!-- Recent Service Requests -->
    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-dark">Recent Service Requests</h6>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Service Type</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $reqs_query = "SELECT sr.*, u.u_name FROM servicereq sr JOIN user_reg u ON sr.u_id = u.ul_id WHERE sr.s_id = ? ORDER BY sr.id DESC LIMIT 5";
                    $reqs = db_fetch_all($con, $reqs_query, "i", [$l_id]);
                    foreach ($reqs as $r):
                        ?>
                        <tr>
                            <td><strong>
                                    <?php echo e($r['u_name']); ?>
                                </strong></td>
                            <td>
                                <?php echo e($r['v_name']); ?>
                            </td>
                            <td>
                                <?php echo e($r['s_name']); ?>
                            </td>
                            <td>
                                <?php echo $r['date']; ?>
                            </td>
                            <td><span class="badge badge-primary px-2 p-1">
                                    <?php echo e($r['status']); ?>
                                </span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($reqs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No service requests yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .text-indigo {
        color: #4338ca;
    }

    .text-teal {
        color: #0d9488;
    }

    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
</style>

<?php include '../templates/footer.php'; ?>