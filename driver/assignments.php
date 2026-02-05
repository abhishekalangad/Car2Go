<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_login();
require_role('driver');

$l_id = $_SESSION['l_id'];

// Handle Status Updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];
    $new_status = ($action === 'confirm') ? 'confirmed' : 'cancelled';

    $q = "UPDATE bookdriver SET d_status = ? WHERE d_id = ? AND dd_id = ?";
    if (db_execute($con, $q, "sii", [$new_status, $id, $l_id])) {
        redirect_with_message('assignments.php', 'Assignment updated successfully.', 'success');
    }
}

// Fetch Assignments
$query = "SELECT b.*, u.u_name as customer_name, u.u_phone, u.u_address, u.u_pincode 
          FROM bookdriver b 
          JOIN user_reg u ON b.dr_id = u.ul_id 
          WHERE b.dd_id = ? 
          ORDER BY b.d_id DESC";
$assignments = db_fetch_all($con, $query, "i", [$l_id]);

$page_title = 'My Assignments - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="container py-5">
    <div class="mb-5">
        <h1 class="font-weight-bold">My <span class="text-primary">Assignments</span></h1>
        <p class="text-muted">Manage your riding requests and confirmed trips.</p>
    </div>

    <div class="row">
        <?php foreach ($assignments as $gig): ?>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-xl overflow-hidden h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h5 class="font-weight-bold mb-1">
                                    <?php echo e($gig['customer_name']); ?>
                                </h5>
                                <span class="badge badge-<?php
                                echo ($gig['d_status'] === 'confirmed') ? 'success' : (($gig['d_status'] === 'Requested') ? 'warning' : 'secondary');
                                ?> pill px-3">
                                    <?php echo e($gig['d_status']); ?>
                                </span>
                            </div>
                            <div class="text-right">
                                <div class="small text-muted mb-1">TRIP DATES</div>
                                <div class="font-weight-bold small text-dark">
                                    <?php echo $gig['d_day1']; ?> to
                                    <?php echo $gig['d_day2']; ?>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light rounded-lg p-3 mb-4">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="extra-small font-weight-bold text-muted uppercase mb-0">Pickup
                                        Location</label>
                                    <div class="small">
                                        <?php echo e($gig['u_address']); ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2 text-right">
                                    <label class="extra-small font-weight-bold text-muted uppercase mb-0">Zip Code</label>
                                    <div class="small">
                                        <?php echo e($gig['u_pincode']); ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="extra-small font-weight-bold text-muted uppercase mb-0">Contact</label>
                                    <div class="small font-weight-bold text-primary"><i class="fas fa-phone-alt mr-1"></i>
                                        <?php echo e($gig['u_phone']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <?php if ($gig['d_status'] === 'Requested'): ?>
                                <div class="d-flex gap-2 w-100">
                                    <a href="assignments.php?action=confirm&id=<?php echo $gig['d_id']; ?>"
                                        class="btn btn-success btn-sm rounded-pill px-4 flex-grow-1 mr-2">Confirm Trip</a>
                                    <a href="assignments.php?action=cancel&id=<?php echo $gig['d_id']; ?>"
                                        class="btn btn-outline-danger btn-sm rounded-pill px-4 flex-grow-1"
                                        onclick="return confirm('Decline this assignment?')">Decline</a>
                                </div>
                            <?php else: ?>
                                <div class="text-muted small italic">Updated on
                                    <?php echo date('d M Y'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($assignments)): ?>
            <div class="col-12 text-center py-5">
                <div class="mb-4"><i class="fas fa-calendar-minus fa-4x text-muted opacity-2"></i></div>
                <h4 class="text-muted">No assignments found.</h4>
                <p>Wait for customers to book your services.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .rounded-xl {
        border-radius: 1.5rem;
    }

    .extra-small {
        font-size: 0.65rem;
    }

    .uppercase {
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pill {
        border-radius: 30px;
    }

    .gap-2 {
        gap: 0.5rem;
    }
</style>

<?php include '../templates/footer.php'; ?>