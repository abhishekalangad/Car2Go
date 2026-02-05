<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

// Ensure only admin can access
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

<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold">Authorized Service Centers</h2>
            <p class="text-muted">Manage partner service centers and workshops.</p>
        </div>
        <div class="btn-group shadow-sm">
            <a href="services.php?filter=all" class="btn btn-white <?php echo $filter === 'all' ? 'active' : ''; ?>">All
                Partners</a>
            <a href="services.php?filter=pending"
                class="btn btn-white <?php echo $filter === 'pending' ? 'active' : ''; ?>">Approval Requests</a>
        </div>
    </div>

    <div class="row">
        <?php foreach ($services as $service): ?>
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow-sm border-0 h-100 rounded-lg">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="bg-primary-light rounded p-3"
                                style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tools fa-2x text-primary"></i>
                            </div>
                            <div class="text-right">
                                <?php if ($service['l_approve'] === 'approve'): ?>
                                    <span class="badge badge-success px-3 py-2 rounded-pill">Verified Partner</span>
                                <?php else: ?>
                                    <span class="badge badge-warning px-3 py-2 rounded-pill">Pending Review</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <h5 class="font-weight-bold mb-1">
                            <?php echo e($service['s_name']); ?>
                        </h5>
                        <p class="text-muted small mb-4"><i class="fas fa-map-marker-alt mr-1"></i>
                            <?php echo e($service['s_address']); ?>
                        </p>

                        <div class="row no-gutters mb-4 bg-light rounded p-3">
                            <div class="col-6 border-right">
                                <small class="text-muted d-block uppercase">Contact</small>
                                <span class="font-weight-bold small">
                                    <?php echo e($service['s_phone']); ?>
                                </span>
                            </div>
                            <div class="col-6 pl-3">
                                <small class="text-muted d-block uppercase">Pincode</small>
                                <span class="font-weight-bold small">
                                    <?php echo e($service['s_pincode']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted d-block mb-2 uppercase">Verification Documents</small>
                            <div class="d-flex gap-2">
                                <a href="../uploads/documents/<?php echo e($service['s_licence']); ?>" target="_blank"
                                    class="btn btn-sm btn-outline-info flex-grow-1">
                                    <i class="fas fa-file-invoice mr-1"></i> License
                                </a>
                                <a href="../uploads/services/<?php echo e($service['s_rc']); ?>" target="_blank"
                                    class="btn btn-sm btn-outline-info flex-grow-1">
                                    <i class="fas fa-image mr-1"></i> Center Photo
                                </a>
                            </div>
                        </div>

                        <div class="pt-3 border-top d-flex gap-2">
                            <?php if ($service['l_approve'] !== 'approve'): ?>
                                <a href="services.php?action=approve&id=<?php echo $service['l_id']; ?>"
                                    class="btn btn-primary flex-grow-1">Approve Partner</a>
                            <?php else: ?>
                                <a href="services.php?action=disapprove&id=<?php echo $service['l_id']; ?>"
                                    class="btn btn-outline-danger flex-grow-1">Suspend Partner</a>
                            <?php endif; ?>
                            <button class="btn btn-light"><i class="fas fa-trash text-danger"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .bg-primary-light {
        background: #eff6ff;
    }

    .uppercase {
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }

    .btn-white {
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
    }

    .btn-white.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
</style>

<?php include '../templates/footer.php'; ?>