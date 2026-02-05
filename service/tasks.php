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

    $q = "UPDATE servicereq SET status = ? WHERE id = ? AND s_id = ?";
    if (db_execute($con, $q, "sii", [$new_status, $id, $l_id])) {
        redirect_with_message('tasks.php', 'Service status updated.', 'success');
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

<div class="container py-5">
    <div class="mb-5">
        <h1 class="font-weight-bold">Service <span class="text-primary">Tasks</span></h1>
        <p class="text-muted">Manage vehicle maintenance requests and ongoing repair tasks.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-xl overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4">Vehicle & Service</th>
                        <th class="py-3">Customer</th>
                        <th class="py-3">Requested Date</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-right px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td class="align-middle px-4">
                                <div class="font-weight-bold">
                                    <?php echo e($task['v_name']); ?>
                                </div>
                                <div class="small text-muted">
                                    <?php echo e($task['s_name']); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold small">
                                    <?php echo e($task['customer_name']); ?>
                                </div>
                                <div class="extra-small text-muted">
                                    <?php echo e($task['u_phone']); ?>
                                </div>
                            </td>
                            <td class="align-middle small">
                                <?php echo $task['date']; ?>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-<?php
                                echo ($task['status'] === 'Approved') ? 'info' : (($task['status'] === 'Completed') ? 'success' : 'warning');
                                ?> pill px-3">
                                    <?php echo e($task['status']); ?>
                                </span>
                            </td>
                            <td class="align-middle text-right px-4">
                                <?php if ($task['status'] === 'Pending'): ?>
                                    <a href="tasks.php?action=approve&id=<?php echo $task['id']; ?>"
                                        class="btn btn-sm btn-outline-primary rounded-pill mr-1">Approve</a>
                                    <a href="tasks.php?action=reject&id=<?php echo $task['id']; ?>"
                                        class="btn btn-sm btn-link text-danger"
                                        onclick="return confirm('Reject this request?')">Reject</a>
                                <?php elseif ($task['status'] === 'Approved'): ?>
                                    <a href="tasks.php?action=complete&id=<?php echo $task['id']; ?>"
                                        class="btn btn-sm btn-success rounded-pill px-3">Mark Complete</a>
                                <?php else: ?>
                                    <span class="text-muted small"><i class="fas fa-check-double"></i> Finished</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No service tasks found.</td>
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

    .extra-small {
        font-size: 0.65rem;
    }

    .pill {
        border-radius: 30px;
    }
</style>

<?php include '../templates/header.php'; ?>