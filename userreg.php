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

<div class="hero-section"
  style="min-height: 100vh; display: flex; align-items: center; padding: 100px 0; background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('images/bg3.jpg'); background-size: cover;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="glass-card p-5">
          <div class="text-center mb-5">
            <h2 class="display-4 font-weight-bold text-white mb-2">Join the <span>Fleet</span></h2>
            <p class="text-white-50">Create your account to start renting premium vehicles.</p>
          </div>

          <?php if ($error_message): ?>
            <div class="alert alert-danger mb-4 shadow-sm border-0"><?php echo e($error_message); ?></div>
          <?php endif; ?>

          <form action="" method="post" enctype="multipart/form-data" class="premium-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="row">
              <div class="col-md-6 form-group mb-4">
                <label class="text-white-50 small uppercase mb-2">Full Name</label>
                <input type="text" name="name" class="form-control bg-transparent text-white border-secondary"
                  placeholder="Enter your full name" required
                  value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>">
              </div>
              <div class="col-md-6 form-group mb-4">
                <label class="text-white-50 small uppercase mb-2">Email Address</label>
                <input type="email" name="email" class="form-control bg-transparent text-white border-secondary"
                  placeholder="name@example.com" required
                  value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 form-group mb-4">
                <label class="text-white-50 small uppercase mb-2">Password</label>
                <input type="password" name="password" class="form-control bg-transparent text-white border-secondary"
                  placeholder="Min. 8 characters" required>
              </div>
              <div class="col-md-6 form-group mb-4">
                <label class="text-white-50 small uppercase mb-2">Phone Number</label>
                <input type="text" name="phone" class="form-control bg-transparent text-white border-secondary"
                  placeholder="10-digit number" pattern="[0-9]{10}" required
                  value="<?php echo isset($_POST['phone']) ? e($_POST['phone']) : ''; ?>">
              </div>
            </div>

            <div class="form-group mb-4">
              <label class="text-white-50 small uppercase mb-2">Mailing Address</label>
              <textarea name="address" class="form-control bg-transparent text-white border-secondary" rows="2"
                placeholder="Street, City, State"
                required><?php echo isset($_POST['address']) ? e($_POST['address']) : ''; ?></textarea>
            </div>

            <div class="row align-items-end">
              <div class="col-md-6 form-group mb-4">
                <label class="text-white-50 small uppercase mb-2">Pincode</label>
                <input type="text" name="pincode" class="form-control bg-transparent text-white border-secondary"
                  placeholder="6-digit ZIP" maxlength="6" pattern="[0-9]{6}" required
                  value="<?php echo isset($_POST['pincode']) ? e($_POST['pincode']) : ''; ?>">
              </div>
              <div class="col-md-6 form-group mb-4">
                <label class="text-white-50 small uppercase mb-2">Identity Proof (License/ID)</label>
                <div class="custom-file">
                  <input type="file" name="licence" class="custom-file-input" id="customFile" required>
                  <label class="custom-file-label bg-transparent border-secondary text-white-50" for="customFile">Choose
                    file</label>
                </div>
              </div>
            </div>

            <div class="alert alert-info border-secondary bg-transparent text-white-50 small mt-3">
              <i class="fas fa-shield-alt mr-2"></i> Your data is processed securely and encrypted at rest.
            </div>

            <div class="text-center mt-5">
              <button type="submit" name="submit" class="btn btn-premium btn-gradient px-5 py-3 font-weight-bold">
                CREATE ACCOUNT
              </button>
              <p class="text-white-50 mt-4 small">
                Already have an account? <a href="login.php" class="text-primary font-weight-bold">Sign In</a>
              </p>
            </div>
          </form>
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

  .custom-file-label::after {
    background: var(--primary-color);
    color: white;
    border: none;
  }
</style>

<script>
  // Show filename on upload
  document.querySelector('.custom-file-input').addEventListener('change', function (e) {
    var fileName = e.target.files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
  });
</script>

<?php include 'templates/footer.php'; ?>