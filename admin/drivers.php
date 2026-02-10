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

<div class="page-hero py-5"
    style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: white; margin-top:-20px; border-radius: 0 0 40px 40px;">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold mb-2">Manage Drivers</h1>
        <p class="opacity-7">Review approvals and manage the driver fleet.</p>

        <div class="btn-group mt-4 shadow-sm rounded-pill overflow-hidden">
            <a href="drivers.php?filter=all"
                class="btn btn-light <?php echo $filter === 'all' ? 'active font-weight-bold text-primary' : 'text-muted'; ?> px-4">All
                Drivers</a>
            <a href="drivers.php?filter=pending"
                class="btn btn-light <?php echo $filter === 'pending' ? 'active font-weight-bold text-primary' : 'text-muted'; ?> px-4">Pending</a>
        </div>
    </div>
</div>

<div class="container py-5 mt-n5">
    <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-4 px-4 border-0 text-uppercase small font-weight-bold text-muted">Driver Profile
                        </th>
                        <th class="py-4 border-0 text-uppercase small font-weight-bold text-muted">Contact Details</th>
                        <th class="py-4 border-0 text-uppercase small font-weight-bold text-muted">Verification</th>
                        <th class="py-4 border-0 text-uppercase small font-weight-bold text-center text-muted">Status
                        </th>
                        <th class="py-4 px-4 border-0 text-uppercase small font-weight-bold text-right text-muted">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($drivers as $driver): ?>
                        <tr>
                            <td class="align-middle px-4">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative">
                                        <div class="bg-light rounded-circle overflow-hidden shadow-sm"
                                            style="width: 50px; height: 50px;">
                                            <img src="../uploads/drivers/<?php echo e($driver['d_proof']); ?>"
                                                class="w-100 h-100" style="object-fit:cover;"
                                                onerror="this.src='../images/default-avatar.png'">
                                        </div>
                                        <?php if ($driver['l_approve'] === 'approve'): ?>
                                            <div class="position-absolute bg-success border border-white rounded-circle"
                                                style="width:12px; height:12px; bottom:2px; right:2px;"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-3">
                                        <div class="font-weight-bold text-dark"><?php echo e($driver['d_name']); ?></div>
                                        <div class="small text-muted font-weight-bold">
                                            ₹<?php echo e($driver['d_amount']); ?>/day</div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="small text-dark mb-1"><i
                                        class="fas fa-envelope mr-2 text-primary opacity-5"></i><?php echo e($driver['d_email']); ?>
                                </div>
                                <div class="small text-dark"><i
                                        class="fas fa-phone mr-2 text-primary opacity-5"></i><?php echo e($driver['d_phone']); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <a href="../uploads/documents/<?php echo e($driver['d_licence']); ?>" target="_blank"
                                    class="btn btn-sm btn-light border rounded-pill px-3 small text-muted">
                                    <i class="fas fa-id-card mr-1"></i> View Licence
                                </a>
                            </td>
                            <td class="align-middle text-center">
                                <?php if ($driver['l_approve'] === 'approve'): ?>
                                    <span class="badge badge-success pill px-3 py-2"><i class="fas fa-check-circle mr-1"></i>
                                        Active</span>
                                <?php else: ?>
                                    <span class="badge badge-warning pill px-3 py-2"><i class="fas fa-clock mr-1"></i>
                                        Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle text-right px-4">
                                <?php if ($driver['l_approve'] !== 'approve'): ?>
                                    <a href="drivers.php?action=approve&id=<?php echo $driver['l_id']; ?>"
                                        class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm font-weight-bold">Approve</a>
                                <?php else: ?>
                                    <a href="drivers.php?action=disapprove&id=<?php echo $driver['l_id']; ?>"
                                        class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm font-weight-bold"
                                        onclick="return confirm('Revoke approval for this driver?')">Revoke</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($drivers)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <img src="../images/empty-state.svg" width="100" class="mb-3 opacity-5">
                                <h5 class="text-muted">No drivers found.</h5>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .rounded-xl {
        border-radius: 1.5rem;
    }

    .pill {
        border-radius: 50px;
    }

    .opacity-5 {
        opacity: 0.5;
    }
</style>

<?php include '../templates/footer.php'; ?>