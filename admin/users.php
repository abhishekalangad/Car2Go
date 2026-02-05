<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

// Ensure only admin can access
require_role('admin');

// Handle Deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // In a real app, use a soft delete or cascading delete
    // For now, simple delete reflecting the old system's intent
    $query1 = "DELETE FROM login WHERE l_id = ? AND l_type = 'user'";
    $query2 = "DELETE FROM user_reg WHERE ul_id = ?";

    if (db_execute($con, $query1, "i", [$id]) && db_execute($con, $query2, "i", [$id])) {
        redirect_with_message('users.php', 'User account deleted successfully.', 'success');
        exit();
    }
}

// Fetch Users
$users = db_fetch_all($con, "SELECT l.*, u.* FROM login l JOIN user_reg u ON l.l_id = u.ul_id WHERE l.l_type = 'user'");

$page_title = 'Manage Users - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="container-fluid py-5">
    <div class="mb-4">
        <h2 class="font-weight-bold">Registered Users</h2>
        <p class="text-muted">Manage regular user accounts and profiles.</p>
    </div>

    <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3">User</th>
                        <th class="py-3">Contact</th>
                        <th class="py-3">Location</th>
                        <th class="py-3">Joined On</th>
                        <th class="py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($user['u_name'], 0, 1)); ?>
                                    </div>
                                    <div class="font-weight-bold">
                                        <?php echo e($user['u_name']); ?>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="small text-muted mb-1"><i class="fas fa-envelope mr-1"></i>
                                    <?php echo e($user['u_email']); ?>
                                </div>
                                <div class="small"><i class="fas fa-phone mr-1"></i>
                                    <?php echo e($user['u_phone']); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="small">
                                    <?php echo e($user['u_address']); ?>
                                </div>
                                <div class="small font-weight-bold text-muted">
                                    <?php echo e($user['u_pincode']); ?>
                                </div>
                            </td>
                            <td class="align-middle small text-muted">--</td>
                            <td class="align-middle text-right">
                                <button class="btn btn-sm btn-outline-primary mr-1"><i class="fas fa-edit"></i></button>
                                <a href="users.php?action=delete&id=<?php echo $user['l_id']; ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>