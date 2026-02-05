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

    // Verify CSRF token or simple check
    $query = "UPDATE login SET l_approve = ? WHERE l_id = ? AND l_type = 'driver'";
    if (db_execute($con, $query, "si", [$new_status, $id])) {
        redirect_with_message('drivers.php', 'Driver status updated successfully.', 'success');
        exit();
    }
}

// Fetch Drivers
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$query = "SELECT l.*, d.* FROM login l JOIN driver_reg d ON l.l_id = d.dl_id WHERE l.l_type = 'driver'";
if ($filter === 'pending') {
    $query .= " AND l.l_approve = 'not approve'";
}
$drivers = db_fetch_all($con, $query);

$page_title = 'Manage Drivers - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold">Manage Drivers</h2>
            <p class="text-muted">Review, approve, or manage driver accounts.</p>
        </div>
        <div class="btn-group">
            <a href="drivers.php?filter=all"
                class="btn btn-outline-primary <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
            <a href="drivers.php?filter=pending"
                class="btn btn-outline-primary <?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending
                Approval</a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Driver</th>
                            <th>Contact Info</th>
                            <th>Amount/Day</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($drivers as $driver): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary-light p-2 mr-3"
                                            style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user-tie text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">
                                                <?php echo e($driver['d_name']); ?>
                                            </div>
                                            <small class="text-muted">ID: #
                                                <?php echo $driver['l_id']; ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="mb-1"><i class="fas fa-envelope mr-1 text-muted"></i>
                                            <?php echo e($driver['d_email']); ?>
                                        </div>
                                        <div><i class="fas fa-phone mr-1 text-muted"></i>
                                            <?php echo e($driver['d_phone']); ?>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="font-weight-bold">₹
                                        <?php echo e($driver['d_amount']); ?>
                                    </span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="../uploads/documents/<?php echo e($driver['d_licence']); ?>"
                                            target="_blank" class="btn btn-outline-info" title="View Licence">
                                            <i class="fas fa-id-card"></i>
                                        </a>
                                        <a href="../uploads/drivers/<?php echo e($driver['d_proof']); ?>" target="_blank"
                                            class="btn btn-outline-info" title="View Profile Photo">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($driver['l_approve'] === 'approve'): ?>
                                        <span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i>
                                            Approved</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning p-2"><i class="fas fa-clock mr-1"></i> Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <?php if ($driver['l_approve'] !== 'approve'): ?>
                                        <a href="drivers.php?action=approve&id=<?php echo $driver['l_id']; ?>"
                                            class="btn btn-sm btn-success mr-1">Approve</a>
                                    <?php else: ?>
                                        <a href="drivers.php?action=disapprove&id=<?php echo $driver['l_id']; ?>"
                                            class="btn btn-sm btn-outline-danger mr-1">Disapprove</a>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($drivers)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                    <h4>No drivers found</h4>
                                    <p class="text-muted">Matches your current filter.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>