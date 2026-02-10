<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_role('admin');

// Handle Status Updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];
    $new_status = ($action === 'approve') ? 'approve' : 'not approve';

    $query = "UPDATE login SET l_approve = ? WHERE l_id = ? AND l_type = 'service center'";
    if (db_execute($con, $query, "si", [$new_status, $id])) {
        redirect_with_message('services.php', 'Service center status updated.', 'success');
        exit();
    }
}

// Fetch Services
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$query = "SELECT l.*, s.* FROM login l JOIN service_reg s ON l.l_id = s.sl_id WHERE l.l_type = 'service center'";
if ($filter === 'pending') {
    $query .= " AND l.l_approve = 'not approve'";
}
$services = db_fetch_all($con, $query);

$page_title = 'Manage Service Centers - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="page-hero py-5"
    style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: white; margin-top:-20px; border-radius: 0 0 40px 40px;">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold mb-2">Service Partners</h1>
        <p class="opacity-7">Manage authorized workshops and service centers.</p>

        <div class="btn-group mt-4 shadow-sm rounded-pill overflow-hidden">
            <a href="services.php?filter=all"
                class="btn btn-light <?php echo $filter === 'all' ? 'active font-weight-bold text-primary' : 'text-muted'; ?> px-4">All
                Partners</a>
            <a href="services.php?filter=pending"
                class="btn btn-light <?php echo $filter === 'pending' ? 'active font-weight-bold text-primary' : 'text-muted'; ?> px-4">Pending
                Requests</a>
        </div>
    </div>
</div>

<div class="container py-5 mt-n5">
    <div class="row">
        <?php foreach ($services as $service): ?>
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow-lg border-0 h-100 rounded-xl service-card">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="bg-light rounded-circle shadow-sm p-3 text-primary d-flex align-items-center justify-content-center"
                                style="width: 65px; height: 65px;">
                                <i class="fas fa-tools fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <?php if ($service['l_approve'] === 'approve'): ?>
                                    <span class="badge badge-success px-3 py-2 pill"><i class="fas fa-certificate mr-1"></i>
                                        Verified</span>
                                <?php else: ?>
                                    <span class="badge badge-warning px-3 py-2 pill"><i class="fas fa-clock mr-1"></i>
                                        Pending</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <h5 class="font-weight-bold mb-1 text-dark">
                            <?php echo e($service['s_name']); ?>
                        </h5>
                        <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt mr-1 text-danger"></i>
                            <?php echo e($service['s_address']); ?></p>

                        <div class="row no-gutters mb-3 bg-light rounded p-3">
                            <div class="col-6 border-right">
                                <small class="text-muted d-block uppercase font-weight-bold"
                                    style="font-size:0.65rem;">Contact</small>
                                <span class="font-weight-bold small text-dark"><?php echo e($service['s_phone']); ?></span>
                            </div>
                            <div class="col-6 pl-3">
                                <small class="text-muted d-block uppercase font-weight-bold"
                                    style="font-size:0.65rem;">Pincode</small>
                                <span
                                    class="font-weight-bold small text-dark"><?php echo e($service['s_pincode']); ?></span>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <div class="mb-3">
                                <div class="d-flex gap-2">
                                    <a href="../uploads/documents/<?php echo e($service['s_licence']); ?>" target="_blank"
                                        class="btn btn-sm btn-light border rounded-pill flex-grow-1 text-muted">
                                        <i class="fas fa-file-contract mr-1"></i> License
                                    </a>
                                    <a href="../uploads/services/<?php echo e($service['s_rc']); ?>" target="_blank"
                                        class="btn btn-sm btn-light border rounded-pill flex-grow-1 text-muted">
                                        <i class="fas fa-image mr-1"></i> Photo
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <?php if ($service['l_approve'] !== 'approve'): ?>
                                    <a href="services.php?action=approve&id=<?php echo $service['l_id']; ?>"
                                        class="btn btn-primary rounded-pill shadow-sm font-weight-bold flex-grow-1">Approve</a>
                                <?php else: ?>
                                    <a href="services.php?action=disapprove&id=<?php echo $service['l_id']; ?>"
                                        class="btn btn-outline-danger rounded-pill font-weight-bold flex-grow-1">Suspend</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($services)): ?>
            <div class="col-12 text-center py-5">
                <img src="../images/empty-state.svg" width="100" class="mb-3 opacity-5">
                <h5 class="text-muted">No service partners found.</h5>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .service-card {
        transition: transform 0.2s;
    }

    .service-card:hover {
        transform: translateY(-5px);
    }

    .rounded-xl {
        border-radius: 1.5rem;
    }

    .pill {
        border-radius: 50px;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .uppercase {
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<?php include '../templates/footer.php'; ?>