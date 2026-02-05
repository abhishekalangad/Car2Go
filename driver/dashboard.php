<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_login();
require_role('driver');

$l_id = $_SESSION['l_id'];

// Fetch driver data
$query = "SELECT d.*, l.l_approve FROM driver_reg d JOIN login l ON d.dl_id = l.l_id WHERE d.dl_id = ?";
$driver = db_fetch_one($con, $query, "i", [$l_id]);

if (!$driver) {
    redirect_with_message('../index.php', 'Profile not found.', 'danger');
}

$page_title = 'Driver Dashboard - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="profile-hero"
    style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%); padding: 60px 0; color: white;">
    <div class="container d-flex align-items-center">
        <img src="../uploads/drivers/<?php echo e($driver['d_proof']); ?>"
            style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;"
            class="mr-4 border border-white">
        <div>
            <h1 class="font-weight-bold mb-1">
                <?php echo e($driver['d_name']); ?>
            </h1>
            <div class="badge badge-success px-3 py-1 rounded-pill">Active Driver</div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center p-4 h-100">
                <div class="text-primary mb-2"><i class="fas fa-calendar-check fa-2x"></i></div>
                <h6 class="font-weight-bold">My Assignments</h6>
                <p class="small text-muted">View your current riding jobs.</p>
                <a href="assignments.php" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center p-4 h-100">
                <div class="text-warning mb-2"><i class="fas fa-star fa-2x"></i></div>
                <h6 class="font-weight-bold">Ratings</h6>
                <p class="small text-muted">Check what customers say about you.</p>
                <a href="../dvfeed.php" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center p-4 h-100">
                <div class="text-success mb-2"><i class="fas fa-id-card fa-2x"></i></div>
                <h6 class="font-weight-bold">My Documents</h6>
                <p class="small text-muted">Manage your license and ID proofs.</p>
                <a href="../driverprofile.php" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center p-4 h-100">
                <div class="text-danger mb-2"><i class="fas fa-cog fa-2x"></i></div>
                <h6 class="font-weight-bold">Settings</h6>
                <p class="small text-muted">Update your daily rate and contact info.</p>
                <a href="../dedit.php" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <!-- Active Gigs -->
    <div class="card border-0 shadow-sm rounded-lg mt-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-dark">Recent Driver Requests</h6>
            <a href="assignments.php" class="small">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light text-uppercase small font-weight-bold">
                    <tr>
                        <th>Customer</th>
                        <th>Location</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // br_id is the driver ID in bookcar table (legacy naming)
                    $gigs_query = "SELECT b.*, u.u_name, u.u_address FROM bookcar b JOIN user_reg u ON b.bo_id = u.ul_id WHERE b.br_id = ? ORDER BY b.b_id DESC LIMIT 5";
                    $gigs = db_fetch_all($con, $gigs_query, "i", [$l_id]);
                    foreach ($gigs as $g):
                        ?>
                        <tr>
                            <td>
                                <?php echo e($g['u_name']); ?>
                            </td>
                            <td><small>
                                    <?php echo e($g['u_address']); ?>
                                </small></td>
                            <td>₹
                                <?php echo e($driver['d_amount']); ?>
                            </td>
                            <td><span class="badge badge-info">
                                    <?php echo e($g['b_status']); ?>
                                </span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($gigs)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">No requests yet. Try setting a competitive daily rate!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>