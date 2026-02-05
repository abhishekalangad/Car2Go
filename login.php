<?php
/**
 * Login Page
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
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Invalid request. Please try again.';
  } else {
    $email = sanitize_input($_POST['l_uname'] ?? '');
    $password = $_POST['l_password'] ?? '';

    if (empty($email) || empty($password)) {
      $error_message = 'Please enter both email and password.';
    } else {
      $query = "SELECT l_id, l_password, l_type, l_approve FROM login WHERE l_uname = ?";
      $user = db_fetch_one($con, $query, "s", [$email]);

      if ($user && verify_password($password, $user['l_password'])) {
        if ($user['l_approve'] === 'approve' || $user['l_type'] === 'admin') {
          $_SESSION['l_id'] = $user['l_id'];
          $_SESSION['l_type'] = $user['l_type'];
          $_SESSION['l_email'] = $email;

          session_regenerate_id(true);

          $redirect_map = [
            'admin' => 'admin/dashboard.php',
            'user' => 'user/dashboard.php',
            'driver' => 'driver/dashboard.php',
            'service center' => 'service/dashboard.php',
            'employe' => 'employee/dashboard.php'
          ];

          $redirect_url = $redirect_map[$user['l_type']] ?? 'index.php';
          redirect_with_message($redirect_url, 'Welcome back! Login successful.', 'success');
        } else {
          $error_message = 'Your account is pending admin approval.';
        }
      } else {
        $error_message = 'Invalid email or password.';
        sleep(1);
      }
    }
  }
}

$page_title = 'Secure Login - CAR2GO';
include 'templates/header.php';
?>

<div class="hero-section" style="min-height: 100vh; display: flex; align-items: center; padding: 100px 0;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="glass-card p-5">
          <div class="text-center mb-5">
            <div class="premium-icon mb-4">
              <i class="fas fa-user-shield fa-3x text-primary"></i>
            </div>
            <h2 class="display-5 font-weight-bold text-white">Welcome <span>Back</span></h2>
            <p class="text-white-50">Securely sign in to your CAR2GO account.</p>
          </div>

          <?php if ($error_message): ?>
            <div class="alert alert-danger mb-4"><?php echo e($error_message); ?></div>
          <?php endif; ?>

          <?php echo display_flash_message(); ?>

          <form action="login.php" method="POST" class="premium-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group mb-4">
              <label class="text-white-50 small mb-2 uppercase">Email Address</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-transparent border-secondary text-white-50"><i
                      class="fas fa-envelope"></i></span>
                </div>
                <input type="email" name="l_uname" class="form-control bg-transparent text-white border-secondary"
                  placeholder="name@example.com" required
                  value="<?php echo isset($_POST['l_uname']) ? e($_POST['l_uname']) : ''; ?>">
              </div>
            </div>

            <div class="form-group mb-5">
              <label class="text-white-50 small mb-2 uppercase">Password</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-transparent border-secondary text-white-50"><i
                      class="fas fa-lock"></i></span>
                </div>
                <input type="password" name="l_password" class="form-control bg-transparent text-white border-secondary"
                  placeholder="••••••••" required>
              </div>
            </div>

            <button type="submit" name="login" class="btn btn-premium btn-gradient w-100 py-3 mb-4 font-weight-bold">
              SIGN IN
            </button>

            <div class="text-center">
              <p class="text-white-50 small mb-4">New to CAR2GO? Register as:</p>
              <div class="d-flex justify-content-center gap-2">
                <a href="userreg.php" class="btn btn-sm btn-outline-light px-3">User</a>
                <a href="driverreg.php" class="btn btn-sm btn-outline-light px-3">Driver</a>
                <a href="servicereg.php" class="btn btn-sm btn-outline-light px-3">Partner</a>
              </div>
            </div>
          </form>
        </div>

        <div class="text-center mt-4">
          <p class="text-white-50 small"><i class="fas fa-lock mr-2"></i> Secured by 256-bit AES Encryption</p>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .uppercase {
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .input-group-text {
    border-right: none;
  }

  .form-control {
    border-left: none;
  }

  .form-control:focus {
    background: rgba(255, 255, 255, 0.05) !important;
    color: white !important;
  }

  .btn-outline-light:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
  }
</style>

<?php include 'templates/footer.php'; ?>