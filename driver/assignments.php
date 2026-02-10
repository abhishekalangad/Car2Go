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
    $new_status = ($action === 'confirm' || $action === 'accept') ? 'confirmed' : 'cancelled';

    // Verify this assignment belongs to the logged-in driver to prevent IDOR
    $check_q = "SELECT d_id FROM bookdriver WHERE d_id = ? AND dd_id = ?";
    if (db_fetch_one($con, $check_q, "ii", [$id, $l_id])) {
        $q = "UPDATE bookdriver SET d_status = ? WHERE d_id = ?";
        if (db_execute($con, $q, "si", [$new_status, $id])) {
            $msg_type = ($new_status === 'confirmed') ? 'success' : 'warning';
            $msg_text = ($new_status === 'confirmed') ? 'Job confirmed! Contact the customer.' : 'Job declined.';
            redirect_with_message('assignments.php', $msg_text, $msg_type);
        }
    } else {
        redirect_with_message('assignments.php', 'Invalid assignment ID.', 'danger');
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

<div class="page-hero py-5"
    style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white; margin-top:-20px; border-radius: 0 0 40px 40px;">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold mb-2">My Assignments</h1>
        <p class="opacity-7">Manage your ride requests and upcoming trips.</p>
    </div>
</div>

<div class="container py-5 mt-n5">
    <div class="row">
        <?php foreach ($assignments as $gig): ?>
            <?php
            $status_class = match ($gig['d_status']) {
                'confirmed', 'Completed' => 'success',
                'Requested' => 'warning',
                'cancelled' => 'danger',
                default => 'secondary'
            };
            ?>
            <div class="col-md-6 col-lg-6 mb-4">
                <div class="card border-0 shadow-lg rounded-xl overflow-hidden h-100 assignment-card">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-3 mr-3 shadow-sm text-primary">
                                    <i class="fas fa-user fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold mb-0 text-dark">
                                        <?php echo e($gig['customer_name']); ?>
                                    </h5>
                                    <div class="small text-muted">Customer</div>
                                </div>
                            </div>
                            <span class="badge badge-<?php echo $status_class; ?> pill px-3 py-1">
                                <?php echo ucfirst($gig['d_status']); ?>
                            </span>
                        </div>

                        <div class="bg-light rounded-lg p-3 mb-4 border border-light">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="extra-small font-weight-bold text-muted uppercase mb-0">From</label>
                                    <div class="small font-weight-bold">
                                        <?php echo date('M d', strtotime($gig['d_day1'])); ?></div>
                                </div>
                                <div class="col-6 text-right">
                                    <label class="extra-small font-weight-bold text-muted uppercase mb-0">To</label>
                                    <div class="small font-weight-bold">
                                        <?php echo date('M d', strtotime($gig['d_day2'])); ?></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="border-top my-2"></div>
                                </div>
                                <div class="col-12">
                                    <label class="extra-small font-weight-bold text-muted uppercase mb-1">Pickup
                                        Location</label>
                                    <div class="small text-dark"><i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                        <?php echo e($gig['u_address']); ?></div>
                                    <div class="small text-muted pl-4">Pincode: <?php echo e($gig['u_pincode']); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <?php if ($gig['d_status'] === 'Requested'): ?>
                                <div class="d-flex gap-2 w-100">
                                    <a href="assignments.php?action=accept&id=<?php echo $gig['d_id']; ?>"
                                        class="btn btn-primary btn-sm rounded-pill px-4 flex-grow-1 mr-2 font-weight-bold shadow-sm">Accept
                                        Job</a>
                                    <a href="assignments.php?action=cancel&id=<?php echo $gig['d_id']; ?>"
                                        class="btn btn-outline-danger btn-sm rounded-pill px-4 flex-grow-1 font-weight-bold"
                                        onclick="return confirm('Decline this assignment?')">Decline</a>
                                </div>
                            <?php elseif ($gig['d_status'] === 'confirmed'): ?>
                                <a href="tel:<?php echo e($gig['u_phone']); ?>"
                                    class="btn btn-outline-primary btn-block rounded-pill font-weight-bold">
                                    <i class="fas fa-phone-alt mr-2"></i> Call Customer
                                </a>
                            <?php else: ?>
                                <button disabled class="btn btn-light btn-block rounded-pill text-muted small">Action
                                    Unavailable</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($assignments)): ?>
            <div class="col-12 text-center py-5">
                <img src="../images/empty-state.svg" width="120" class="mb-3 opacity-5">
                <h4 class="text-muted">No assignments found.</h4>
                <p>Wait for customers to book your services.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .assignment-card {
        transition: transform 0.2s;
    }

    .assignment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
    }

    .rounded-xl {
        border-radius: 1.25rem;
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
</style>

<?php include '../templates/header.php'; ?>