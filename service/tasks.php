<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_login();
require_role('service center');

$l_id = $_SESSION['l_id'];

// Handle Status Updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];
    $new_status = ($action === 'approve') ? 'Approved' : (($action === 'complete') ? 'Completed' : 'Rejected');

    // Verify ownership
    $check_q = "SELECT id FROM servicereq WHERE id = ? AND s_id = ?";
    if (db_fetch_one($con, $check_q, "ii", [$id, $l_id])) {
        $q = "UPDATE servicereq SET status = ? WHERE id = ?";
        if (db_execute($con, $q, "si", [$new_status, $id])) {
            redirect_with_message('tasks.php', 'Service request updated.', 'success');
        }
    } else {
        redirect_with_message('tasks.php', 'Unauthorized action.', 'danger');
    }
}

// Fetch Tasks
$query = "SELECT sr.*, u.u_name as customer_name, u.u_phone 
          FROM servicereq sr 
          JOIN user_reg u ON sr.u_id = u.ul_id 
          WHERE sr.s_id = ? 
          ORDER BY sr.id DESC";
$tasks = db_fetch_all($con, $query, "i", [$l_id]);

$page_title = 'Service Tasks - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="page-hero py-5"
    style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: white; margin-top:-20px; border-radius: 0 0 40px 40px;">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold mb-2">Service Queue</h1>
        <p class="opacity-7">Track vehicle maintenance and repair jobs.</p>
    </div>
</div>

<div class="container py-5 mt-n5">
    <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-4 px-4 border-0 text-uppercase small font-weight-bold text-muted">Vehicle Details
                        </th>
                        <th class="py-4 border-0 text-uppercase small font-weight-bold text-muted">Customer</th>
                        <th class="py-4 border-0 text-uppercase small font-weight-bold text-muted">Date & Note</th>
                        <th class="py-4 border-0 text-uppercase small font-weight-bold text-center text-muted">Status
                        </th>
                        <th class="py-4 px-4 border-0 text-uppercase small font-weight-bold text-right text-muted">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td class="align-middle px-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-3 mr-3 text-primary">
                                        <i class="fas fa-car-crash fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark"><?php echo e($task['v_name']); ?></div>
                                        <div class="small text-muted"><?php echo e($task['s_name']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark"><?php echo e($task['customer_name']); ?></div>
                                <div class="small text-muted"><i class="fas fa-phone-alt mr-1"></i>
                                    <?php echo e($task['u_phone']); ?></div>
                            </td>
                            <td class="align-middle">
                                <div class="text-dark small font-weight-bold">
                                    <?php echo date('M d, Y', strtotime($task['date'])); ?></div>
                                <div class="small text-muted text-truncate" style="max-width: 150px;"
                                    title="<?php echo e($task['rev']); ?>">
                                    "<?php echo e($task['rev']); ?>"
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                <?php
                                $s_class = match ($task['status']) {
                                    'Approved' => 'info',
                                    'Completed' => 'success',
                                    'Pending' => 'warning',
                                    default => 'secondary'
                                };
                                $s_icon = match ($task['status']) {
                                    'Approved' => 'fa-spinner fa-spin',
                                    'Completed' => 'fa-check',
                                    'Pending' => 'fa-clock',
                                    default => 'fa-circle'
                                };
                                ?>
                                <span class="badge badge-<?php echo $s_class; ?> pill px-3 py-2">
                                    <i class="fas <?php echo $s_icon; ?> mr-1 small"></i> <?php echo e($task['status']); ?>
                                </span>
                            </td>
                            <td class="align-middle text-right px-4">
                                <?php if ($task['status'] === 'Pending'): ?>
                                    <a href="tasks.php?action=approve&id=<?php echo $task['id']; ?>"
                                        class="btn btn-sm btn-primary rounded-pill px-3 mr-1 shadow-sm font-weight-bold">Approve</a>
                                    <a href="tasks.php?action=reject&id=<?php echo $task['id']; ?>"
                                        class="btn btn-sm btn-light text-danger rounded-circle"
                                        onclick="return confirm('Reject this request?')" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php elseif ($task['status'] === 'Approved'): ?>
                                    <a href="tasks.php?action=complete&id=<?php echo $task['id']; ?>"
                                        class="btn btn-sm btn-success rounded-pill px-3 shadow-sm font-weight-bold">Mark
                                        Done</a>
                                <?php else: ?>
                                    <span class="text-muted small italic"><i class="fas fa-check-double text-success"></i>
                                        Closed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <img src="../images/empty-state.svg" width="80" class="mb-3 opacity-5">
                                <h5 class="text-muted">No service requests.</h5>
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
</style>

<?php include '../templates/header.php'; ?>