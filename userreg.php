<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Redirect if already logged in
if (is_logged_in()) {
  header("Location: profile.php");
  exit();
}

$error_message = '';
$success_message = '';

if (isset($_POST['submit'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Invalid request.';
  } else {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $address = sanitize_input($_POST['address']);
    $pincode = sanitize_input($_POST['pincode']);
    $phone = sanitize_input($_POST['phone']);

    if (!validate_email($email)) {
      $error_message = 'Invalid email address.';
    } else if (!validate_password($password)) {
      $error_message = 'Password must be 8-15 chars, with upper, lower & number.';
    } else if (!validate_pincode($pincode)) {
      $error_message = 'Invalid 6-digit pincode.';
    } else if (!validate_phone($phone)) {
      $error_message = 'Invalid 10-digit phone number.';
    } else {
      $file_validation = validate_file_upload($_FILES['licence'], ['image/jpeg', 'image/png', 'application/pdf'], 5 * 1024 * 1024);

      if ($file_validation !== true) {
        $error_message = 'ID Proof error: ' . $file_validation;
      } else {
        // Check if email already exists
        $check_email = db_fetch_one($con, "SELECT l_id FROM login WHERE l_uname = ?", "s", [$email]);
        if ($check_email) {
          $error_message = 'Email address is already registered.';
        } else {
          $hashed_password = hash_password($password);
          $type = 'user';
          $approve = 'approve';

          if (db_execute($con, "INSERT INTO login (l_uname, l_password, l_type, l_approve) VALUES (?, ?, ?, ?)", "ssss", [$email, $hashed_password, $type, $approve])) {
            $id = $con->insert_id;
            $licence_filename = generate_unique_filename($_FILES['licence']['name']);

            $query = "INSERT INTO user_reg (ul_id, u_name, u_email, u_password, u_address, u_pincode, u_phone, u_licence) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            if (db_execute($con, $query, "isssssss", [$id, $name, $email, $hashed_password, $address, $pincode, $phone, $licence_filename])) {

              $upload_dir = "uploads/documents/";
              if (!is_dir($upload_dir))
                mkdir($upload_dir, 0755, true);

              move_uploaded_file($_FILES['licence']['tmp_name'], $upload_dir . $licence_filename);
              redirect_with_message('login.php', 'Registration successful! Please sign in.', 'success');
            } else {
              $error_message = 'Error saving profile details.';
            }
          } else {
            $error_message = 'Registration failed. Please try again.';
          }
        }
      }
    }
  }
}

$page_title = 'Join CAR2GO - Premium User Registration';
include 'templates/header.php';
?>

<style>
  body {
    background: #f8fafc;
  }

  .register-container {
    display: flex;
    box-shadow: 0 0 50px rgba(0, 0, 0, 0.08);
    border-radius: 20px;
    overflow: hidden;
    margin: 50px auto;
    max-width: 1200px;
    background: white;
    min-height: 80vh;
  }

  .register-visual {
    flex: 1;
    background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('images/bg3.jpg') center/cover;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 4rem;
    color: white;
    position: relative;
  }

  .register-content {
    position: relative;
    z-index: 2;
  }

  .form-wrapper {
    flex: 1.2;
    padding: 4rem;
    overflow-y: auto;
  }

  .form-control-lg {
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 0.95rem;
    padding: 1.25rem 1rem;
  }

  .form-control-lg:focus {
    background: white;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  }

  .file-upload-box {
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #f8fafc;
  }

  .file-upload-box:hover {
    border-color: #3b82f6;
    background: #eff6ff;
  }

  .step-indicator {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
  }

  .step-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #e2e8f0;
    margin-right: 10px;
  }

  .step-dot.active {
    background: #3b82f6;
    width: 25px;
    border-radius: 10px;
  }

  @media (max-width: 992px) {
    .register-visual {
      display: none;
    }

    .register-container {
      margin: 20px;
    }

    .form-wrapper {
      padding: 2rem;
    }
  }
</style>

<div class="container">
  <div class="register-container">
    <!-- Left: Visual Side -->
    <div class="register-visual">
      <div class="register-content">
        <h2 class="font-weight-bold display-5 mb-4">Join the<br>Elite Fleet.</h2>
        <p class="lead opacity-8 mb-4">Create your account to unlock premium rentals and professional services.</p>
        <div class="mt-4">
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-check-circle text-success mr-3 fa-lg"></i>
            <span class="font-weight-bold">Instant Booking</span>
          </div>
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-check-circle text-success mr-3 fa-lg"></i>
            <span class="font-weight-bold">Exclusive Deals</span>
          </div>
          <div class="d-flex align-items-center">
            <i class="fas fa-check-circle text-success mr-3 fa-lg"></i>
            <span class="font-weight-bold">Priority Support</span>
          </div>
        </div>
      </div>
      <div style="position: absolute; bottom: 4rem; left: 4rem; opacity: 0.6; font-size: 0.9rem;">
        &copy; 2024 CAR2GO Inc.
      </div>
    </div>

    <!-- Right: Registration Form -->
    <div class="form-wrapper">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="font-weight-bold text-dark mb-1">Create Account</h2>
          <p class="text-muted small">Fill in your details to get started.</p>
        </div>
        <div class="step-indicator">
          <div class="step-dot active"></div>
          <div class="step-dot"></div>
        </div>
      </div>

      <?php if ($error_message): ?>
        <div class="alert alert-danger mb-4 shadow-sm border-0 rounded-lg">
          <i class="fas fa-exclamation-circle mr-2"></i> <?php echo e($error_message); ?>
        </div>
      <?php endif; ?>

      <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <h6 class="text-uppercase text-muted font-weight-bold small mb-3">Personal Details</h6>
        <div class="row">
          <div class="col-md-6 mb-3">
            <input type="text" name="name" class="form-control form-control-lg" placeholder="Full Name" required>
          </div>
          <div class="col-md-6 mb-3">
            <input type="text" name="phone" class="form-control form-control-lg" placeholder="Mobile Number" required>
          </div>
        </div>

        <div class="mb-3">
          <input type="text" name="address" class="form-control form-control-lg" placeholder="Residential Address"
            required>
        </div>

        <div class="row mb-4">
          <div class="col-md-6">
            <input type="text" name="pincode" class="form-control form-control-lg" placeholder="Pincode" required>
          </div>
        </div>

        <h6 class="text-uppercase text-muted font-weight-bold small mb-3 mt-4">Account Security</h6>
        <div class="mb-3">
          <input type="email" name="email" class="form-control form-control-lg" placeholder="Email Address" required>
        </div>
        <div class="mb-4">
          <input type="password" name="password" class="form-control form-control-lg" placeholder="Create Password"
            required>
          <small class="text-muted">Must contain 8 chars, uppercase, lowercase & number</small>
        </div>

        <h6 class="text-uppercase text-muted font-weight-bold small mb-3 mt-4">Verification</h6>
        <div class="file-upload-box mb-4 position-relative">
          <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
          <h6 class="font-weight-bold">Upload ID Proof</h6>
          <p class="small text-muted mb-0">Drag & drop or click to upload (License/Passport)</p>
          <input type="file" name="licence" class="position-absolute w-100 h-100"
            style="top:0; left:0; opacity:0; cursor:pointer;" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow-sm"
          style="background: linear-gradient(135deg, #3b82f6, #2563eb); border:none; border-radius: 12px; padding: 12px;">
          COMPLETE REGISTRATION
        </button>

        <div class="text-center mt-4">
          <p class="text-muted small">Already have an account? <a href="login.php"
              class="font-weight-bold text-primary">Sign In</a></p>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>