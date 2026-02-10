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

<style>
  body {
    background: #f8fafc;
  }

  .login-container {
    min-height: 85vh;
    display: flex;
    box-shadow: 0 0 50px rgba(0, 0, 0, 0.1);
    border-radius: 20px;
    overflow: hidden;
    margin: 50px auto;
    max-width: 1000px;
    background: white;
  }

  .login-visual {
    flex: 1;
    background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.7)), url('images/bg8.jpg') center/cover;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 4rem;
    color: white;
    position: relative;
  }

  .login-visual::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    backdrop-filter: blur(2px);
  }

  .login-content {
    position: relative;
    z-index: 2;
  }

  .login-form-wrapper {
    flex: 1;
    padding: 4rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .form-control-lg {
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 1rem;
    padding: 1.5rem 1rem;
  }

  .form-control-lg:focus {
    background: white;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  }

  .social-btn {
    width: 100%;
    border: 1px solid #e2e8f0;
    background: white;
    padding: 10px;
    border-radius: 10px;
    font-weight: 600;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
  }

  .social-btn:hover {
    background: #f8fafc;
    transform: translateY(-2px);
  }

  .divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: #94a3b8;
    margin: 1.5rem 0;
  }

  .divider::before,
  .divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e2e8f0;
  }

  .divider span {
    padding: 0 10px;
    font-size: 0.85rem;
    text-transform: uppercase;
  }

  .feature-item {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
  }

  .feature-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
  }

  @media (max-width: 992px) {
    .login-visual {
      display: none;
    }

    .login-container {
      margin: 20px;
    }
  }
</style>

<div class="container">
  <div class="login-container">
    <!-- Left: Visual Side -->
    <div class="login-visual">
      <div class="login-content">
        <h2 class="font-weight-bold display-5 mb-4">Drive Your<br>Dreams Today.</h2>
        <div class="feature-item">
          <div class="feature-icon"><i class="fas fa-check"></i></div>
          <div>
            <h5 class="mb-0">Verified Partners</h5>
            <p class="small text-white-50 mb-0">100% Trusted Network</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
          <div>
            <h5 class="mb-0">Secure Booking</h5>
            <p class="small text-white-50 mb-0">Protected Payments</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i class="fas fa-headset"></i></div>
          <div>
            <h5 class="mb-0">24/7 Support</h5>
            <p class="small text-white-50 mb-0">Always here for you</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: Login Form -->
    <div class="login-form-wrapper">
      <div class="mb-4">
        <h2 class="font-weight-bold text-dark">Welcome Back</h2>
        <p class="text-muted">Enter your credentials to access your account.</p>
      </div>

      <?php if ($error_message): ?>
        <div class="alert alert-danger mb-4 shadow-sm border-0 rounded-lg">
          <i class="fas fa-exclamation-circle mr-2"></i> <?php echo e($error_message); ?>
        </div>
      <?php endif; ?>

      <?php echo display_flash_message(); ?>

      <form action="login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <div class="form-group mb-4">
          <label class="small font-weight-bold text-muted ml-1">Email Address</label>
          <input type="email" name="l_uname" class="form-control form-control-lg" placeholder="name@company.com"
            required>
        </div>

        <div class="form-group mb-4">
          <div class="d-flex justify-content-between">
            <label class="small font-weight-bold text-muted ml-1">Password</label>
            <a href="#" class="small text-primary font-weight-bold">Forgot?</a>
          </div>
          <input type="password" name="l_password" class="form-control form-control-lg" placeholder="••••••••" required>
        </div>

        <button type="submit" name="login" class="btn btn-primary btn-block btn-lg font-weight-bold shadow-sm"
          style="background: linear-gradient(135deg, #3b82f6, #2563eb); border:none; border-radius: 12px; padding: 12px;">
          SIGN IN
        </button>
      </form>

      <div class="divider"><span>Or continue with</span></div>

      <div class="row">
        <div class="col-6 pr-2">
          <a href="#" class="social-btn"><i class="fab fa-google text-danger mr-2"></i> Google</a>
        </div>
        <div class="col-6 pl-2">
          <a href="#" class="social-btn"><i class="fab fa-facebook-f text-primary mr-2"></i> Facebook</a>
        </div>
      </div>

      <div class="text-center mt-5">
        <p class="text-muted mb-0">Don't have an account? <a href="userreg.php"
            class="font-weight-bold text-primary">Register Free</a></p>
      </div>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>