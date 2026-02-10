<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_role('admin');

// Handle Deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
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

<div class="page-hero py-5"
    style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: white; margin-top:-20px; border-radius: 0 0 40px 40px;">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold mb-2">Registered Users</h1>
        <p class="opacity-7">Manage customer accounts and profiles.</p>
    </div>
</div>

<div class="container py-5 mt-n5">
    <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-4 px-4 border-0 text-uppercase small font-weight-bold text-muted">Customer</th>
                        <th class="py-4 border-0 text-uppercase small font-weight-bold text-muted">Contact Info</th>
                        <th class="py-4 border-0 text-uppercase small font-weight-bold text-muted">Location</th>
                        <th class="py-4 px-4 border-0 text-uppercase small font-weight-bold text-right text-muted">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="align-middle px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 45px; height: 45px; font-weight:bold;">
                                        <?php echo strtoupper(substr($user['u_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark"><?php echo e($user['u_name']); ?></div>
                                        <div class="small text-muted">User ID: #<?php echo $user['ul_id']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="small text-dark mb-1"><i
                                        class="fas fa-envelope mr-2 text-primary opacity-5"></i><?php echo e($user['u_email']); ?>
                                </div>
                                <div class="small text-dark"><i
                                        class="fas fa-phone mr-2 text-primary opacity-5"></i><?php echo e($user['u_phone']); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="small text-dark"><?php echo e($user['u_address']); ?></div>
                                <span
                                    class="badge badge-light border text-muted mt-1"><?php echo e($user['u_pincode']); ?></span>
                            </td>
                            <td class="align-middle text-right px-4">
                                <button class="btn btn-sm btn-light text-primary rounded-circle shadow-sm mr-2"
                                    title="Edit (Coming Soon)">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <a href="users.php?action=delete&id=<?php echo $user['l_id']; ?>"
                                    class="btn btn-sm btn-light text-danger rounded-circle shadow-sm"
                                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                                    title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No users found.</td>
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

    .opacity-5 {
        opacity: 0.5;
    }
</style>

<?php include '../templates/footer.php'; ?>