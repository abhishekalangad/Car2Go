<?php
/**
 * Login Page - Secure Version
 * Handles user authentication with password hashing and CSRF protection
 */

session_start();

require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Redirect if already logged in
if (is_logged_in()) {
    $redirect_map = [
        'admin' => 'admin/dashboard.php',
        'user' => 'user/dashboard.php',
        'driver' => 'driver/dashboard.php',
        'service center' => 'service/dashboard.php',
        'employe' => 'employee/dashboard.php'
    ];

    $user_type = $_SESSION['l_type'] ?? 'user';
    $redirect_url = $redirect_map[$user_type] ?? 'index.php';
    header("Location: $redirect_url");
    exit();
}

$error_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid request. Please try again.';
    } else {
        // Sanitize inputs
        $email = sanitize_input($_POST['l_uname'] ?? '');
        $password = $_POST['l_password'] ?? '';

        // Validate inputs
        if (empty($email) || empty($password)) {
            $error_message = 'Please enter both email and password.';
        } else if (!validate_email($email)) {
            $error_message = 'Please enter a valid email address.';
        } else {
            // Fetch user from database using prepared statement
            $query = "SELECT l_id, l_password, l_type, l_approve FROM login WHERE l_uname = ?";
            $user = db_fetch_one($con, $query, "s", [$email]);

            if ($user && verify_password($password, $user['l_password'])) {
                // Check if user is approved
                if ($user['l_approve'] === 'approve') {
                    // Set session variables
                    $_SESSION['l_id'] = $user['l_id'];
                    $_SESSION['l_type'] = $user['l_type'];
                    $_SESSION['l_email'] = $email;

                    // Regenerate session ID for security
                    session_regenerate_id(true);

                    // Redirect based on user type
                    $redirect_map = [
                        'admin' => 'admin/dashboard.php',
                        'user' => 'user/dashboard.php',
                        'driver' => 'driver/dashboard.php',
                        'service center' => 'service/dashboard.php',
                        'employe' => 'employee/dashboard.php'
                    ];

                    $redirect_url = $redirect_map[$user['l_type']] ?? 'index.php';

                    redirect_with_message($redirect_url, 'Login successful! Welcome back.', 'success');
                } else {
                    $error_message = 'Your account is pending approval. Please wait for admin approval.';
                }
            } else {
                // Generic error message to prevent user enumeration
                $error_message = 'Invalid email or password.';

                // Add a small delay to prevent brute force attacks
                sleep(1);
            }
        }
    }
}

// Include header
include 'templates/header.php';
?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">CAR2GO Login</h3>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e($error_message); ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    <?php endif; ?>

                    <?php echo display_flash_message(); ?>

                    <form action="<?php echo e($_SERVER['PHP_SELF']); ?>" method="POST">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="l_uname"
                                placeholder="Enter your email" required autocomplete="email"
                                value="<?php echo isset($_POST['l_uname']) ? e($_POST['l_uname']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="l_password"
                                placeholder="Enter your password" required autocomplete="current-password">
                        </div>

                        <button type="submit" name="login" class="btn btn-primary btn-block">
                            <i class="fa fa-sign-in"></i> Login
                        </button>
                    </form>

                    <hr>

                    <div class="text-center">
                        <p class="mb-2">Don't have an account?</p>
                        <div class="btn-group-vertical w-100">
                            <a href="register_user.php" class="btn btn-outline-primary btn-sm mb-1">
                                Register as User
                            </a>
                            <a href="register_driver.php" class="btn btn-outline-success btn-sm mb-1">
                                Register as Driver
                            </a>
                            <a href="register_service.php" class="btn btn-outline-info btn-sm">
                                Register as Service Center
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fa fa-lock"></i> Your connection is secure.
                    We use industry-standard encryption.
                </small>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>