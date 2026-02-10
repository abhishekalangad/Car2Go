<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();

$l_id = $_SESSION['l_id'];
$success_message = '';
$error_message = '';

// Handle Password Change
if (isset($_POST['change_password'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid request.';
    } else {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = 'Please fill all password fields.';
        } else if ($new_password !== $confirm_password) {
            $error_message = 'New passwords do not match.';
        } else if (!validate_password($new_password)) {
            $error_message = 'New password must be 8-15 chars, with upper, lower & number.';
        } else {
            // Verify current password
            $query = "SELECT l_password FROM login WHERE l_id = ?";
            $user = db_fetch_one($con, $query, "i", [$l_id]);

            if ($user && verify_password($current_password, $user['l_password'])) {
                $hashed_new_password = hash_password($new_password);
                $update_query = "UPDATE login SET l_password = ? WHERE l_id = ?";
                if (db_execute($con, $update_query, "si", [$hashed_new_password, $l_id])) {
                    $success_message = 'Password updated successfully.';
                } else {
                    $error_message = 'Failed to update password.';
                }
            } else {
                $error_message = 'Incorrect current password.';
            }
        }
    }
}

$page_title = 'Account Settings - CAR2GO';
include 'templates/header.php';
?>

<div class="page-hero">
    <div class="container hero-content text-center">
        <h1 class="font-weight-bold mb-2 animate__animated animate__fadeInDown">Account <span
                class="text-primary">Settings</span></h1>
        <p class="lead text-white-50 animate__animated animate__fadeInUp">Manage your security preferences.</p>
    </div>
</div>

<div class="container animate__animated animate__fadeInUp animate__delay-1s"
    style="margin-top: -80px; position: relative; z-index: 10;">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <ul class="nav nav-pills nav-fill" id="settingsTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold" id="security-tab" data-toggle="pill"
                                href="#security" role="tab" aria-controls="security" aria-selected="true">
                                <i class="fas fa-lock mr-2"></i> Security
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-muted" href="profile.php">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-5">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success rounded-lg shadow-sm mb-4">
                            <i class="fas fa-check-circle mr-2"></i>
                            <?php echo e($success_message); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger rounded-lg shadow-sm mb-4">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?php echo e($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <div class="tab-content" id="settingsTabContent">
                        <!-- Security / Password Tab -->
                        <div class="tab-pane fade show active" id="security" role="tabpanel"
                            aria-labelledby="security-tab">
                            <h5 class="font-weight-bold text-dark mb-4">Change Password</h5>
                            <form action="" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                                <div class="form-group mb-4">
                                    <label class="small text-uppercase font-weight-bold text-muted">Current
                                        Password</label>
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0"><i
                                                    class="fas fa-key text-muted"></i></span>
                                        </div>
                                        <input type="password" name="current_password"
                                            class="form-control bg-light border-0" placeholder="Enter current password"
                                            required>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="small text-uppercase font-weight-bold text-muted">New Password</label>
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0"><i
                                                    class="fas fa-lock text-muted"></i></span>
                                        </div>
                                        <input type="password" name="new_password"
                                            class="form-control bg-light border-0" placeholder="Enter new password"
                                            required>
                                    </div>
                                    <small class="form-text text-muted mt-2">
                                        <i class="fas fa-info-circle mr-1"></i> 8-15 characters, including upper, lower
                                        case & number.
                                    </small>
                                </div>

                                <div class="form-group mb-5">
                                    <label class="small text-uppercase font-weight-bold text-muted">Confirm New
                                        Password</label>
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0"><i
                                                    class="fas fa-check-circle text-muted"></i></span>
                                        </div>
                                        <input type="password" name="confirm_password"
                                            class="form-control bg-light border-0" placeholder="Repeat new password"
                                            required>
                                    </div>
                                </div>

                                <button type="submit" name="change_password"
                                    class="btn btn-primary btn-block btn-lg rounded-pill font-weight-bold shadow-sm hover-lift">
                                    Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 5rem 0 7rem;
        color: white;
        margin-top: -20px;
        position: relative;
        overflow: hidden;
    }

    .rounded-xl {
        border-radius: 20px;
    }

    .nav-pills .nav-link.active {
        background-color: #eff6ff;
        color: #2563eb;
    }

    .nav-pills .nav-link {
        color: #64748b;
        border-radius: 10px;
        padding: 12px 20px;
        transition: all 0.2s;
    }

    .nav-pills .nav-link:hover {
        background-color: #f8fafc;
        color: #2563eb;
    }

    .form-control:focus {
        background-color: white !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .hover-lift {
        transition: transform 0.2s;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
    }
</style>

<?php include 'templates/footer.php'; ?>