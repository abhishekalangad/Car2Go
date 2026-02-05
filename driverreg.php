<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

$success_message = '';
$error_message = '';

if (isset($_POST['submit'])) {
  // Verify CSRF token
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Invalid request. Please try again.';
  } else {
    // Sanitize and validate inputs
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $d_amt = sanitize_input($_POST['d_amt']);
    $address = sanitize_input($_POST['address']);
    $pincode = sanitize_input($_POST['pincode']);
    $phone = sanitize_input($_POST['phone']);

    // Basic validation
    if (!validate_email($email)) {
      $error_message = 'Invalid email address';
    } else if (!validate_password($password)) {
      $error_message = 'Password must be 8-15 characters with uppercase, lowercase, and number';
    } else if (!validate_phone($phone)) {
      $error_message = 'Invalid phone number (must be 10 digits)';
    } else if (!is_numeric($d_amt) || $d_amt <= 0) {
      $error_message = 'Please enter a valid amount per day';
    } else {
      // Validate file uploads
      $licence_valid = validate_file_upload($_FILES['licence'], ['image/jpeg', 'image/png', 'application/pdf'], 2 * 1024 * 1024);
      $photo_valid = validate_file_upload($_FILES['rc'], ['image/jpeg', 'image/png'], 2 * 1024 * 1024);

      if ($licence_valid !== true) {
        $error_message = "Licence upload error: " . $licence_valid;
      } else if ($photo_valid !== true) {
        $error_message = "Photo proof upload error: " . $photo_valid;
      } else {
        // Check if email already exists
        $check_query = "SELECT l_id FROM login WHERE l_uname = ?";
        if (db_fetch_one($con, $check_query, "s", [$email])) {
          $error_message = 'Email address is already registered';
        } else {
          // Start transaction (manual via db_execute)
          $hashed_password = hash_password($password);
          $type = 'driver';
          $approve = 'not approve'; // Drivers need admin approval

          $query1 = "INSERT INTO login (l_uname, l_password, l_type, l_approve) VALUES (?, ?, ?, ?)";
          if (db_execute($con, $query1, "ssss", [$email, $hashed_password, $type, $approve])) {
            $id = $con->insert_id;

            // Generate unique filenames
            $licence_filename = generate_unique_filename($_FILES['licence']['name']);
            $photo_filename = generate_unique_filename($_FILES['rc']['name']);

            $query2 = "INSERT INTO driver_reg (dl_id, d_name, d_email, d_password, d_address, d_pincode, d_phone, d_licence, d_proof, d_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            if (db_execute($con, $query2, "isssssssss", [$id, $name, $email, $hashed_password, $address, $pincode, $phone, $licence_filename, $photo_filename, $d_amt])) {

              // Define upload directories
              $doc_dir = "uploads/documents/";
              $driver_dir = "uploads/drivers/";

              // Ensure directories exist
              if (!is_dir($doc_dir))
                mkdir($doc_dir, 0755, true);
              if (!is_dir($driver_dir))
                mkdir($driver_dir, 0755, true);

              // Move files
              $licence_moved = move_uploaded_file($_FILES['licence']['tmp_name'], $doc_dir . $licence_filename);
              $photo_moved = move_uploaded_file($_FILES['rc']['tmp_name'], $driver_dir . $photo_filename);

              if ($licence_moved && $photo_moved) {
                redirect_with_message('login.php', 'Driver registration successful! Please wait for admin approval before logging in.', 'success');
                exit();
              } else {
                $error_message = 'Error saving uploaded files. Please contact support.';
              }
            } else {
              $error_message = 'Failed to save driver profile details.';
            }
          } else {
            $error_message = 'Failed to create login account.';
          }
        }
      }
    }
  }
}

$page_title = 'Driver Registration - CAR2GO';
include 'templates/header.php';
?>

<div class="hero-section" style="height: auto; padding: 100px 0;">
  <div class="container d-flex justify-content-center">
    <div class="glass-card" style="width: 100%; max-width: 800px; padding: 40px;">
      <div class="text-center mb-5">
        <h2 class="display-4 font-weight-bold" style="color: white; letter-spacing: -2px;">Join Our <span>Driver
            Fleet</span></h2>
        <p class="text-white-50">Register today and start earning on your own terms.</p>
      </div>

      <?php if ($error_message): ?>
        <div class="alert alert-danger mb-4"><?php echo e($error_message); ?></div>
      <?php endif; ?>

      <form action="#" method="post" enctype="multipart/form-data" class="premium-form">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Full Name</label>
            <input type="text" class="form-control bg-transparent text-white border-secondary" name="name"
              placeholder="Enter your full name" required
              value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Email Address</label>
            <input type="email" class="form-control bg-transparent text-white border-secondary" name="email"
              placeholder="Enter your email" required
              value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Password</label>
            <input type="password" class="form-control bg-transparent text-white border-secondary" name="password"
              placeholder="Min. 8 chars, 1 uppercase, 1 number" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Per Day Amount (₹)</label>
            <input type="number" class="form-control bg-transparent text-white border-secondary" name="d_amt"
              placeholder="Amount per day" required
              value="<?php echo isset($_POST['d_amt']) ? e($_POST['d_amt']) : ''; ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="text-white small mb-1">Current Address</label>
          <textarea name="address" class="form-control bg-transparent text-white border-secondary" rows="3"
            placeholder="Enter your complete address"
            required><?php echo isset($_POST['address']) ? e($_POST['address']) : ''; ?></textarea>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Pincode</label>
            <input type="text" class="form-control bg-transparent text-white border-secondary" name="pincode"
              placeholder="6-digit pincode" required maxlength="6"
              value="<?php echo isset($_POST['pincode']) ? e($_POST['pincode']) : ''; ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Phone Number</label>
            <input type="text" class="form-control bg-transparent text-white border-secondary" name="phone"
              placeholder="10-digit mobile number" required pattern="[0-9]{10}" maxlength="10"
              value="<?php echo isset($_POST['phone']) ? e($_POST['phone']) : ''; ?>">
          </div>
        </div>

        <div class="row mb-5 mt-4">
          <div class="col-md-6 mb-3">
            <div class="p-3 border border-secondary rounded" style="background: rgba(0,0,0,0.2);">
              <label class="text-white font-weight-bold mb-2"><i class="fas fa-id-card-alt mr-2 text-primary"></i>
                Driving Licence</label>
              <input type="file" name="licence" class="text-white-50 small d-block" required
                accept=".jpg,.jpeg,.png,.pdf">
              <small class="text-muted mt-1 d-block">JPG, PNG or PDF (Max 2MB)</small>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="p-3 border border-secondary rounded" style="background: rgba(0,0,0,0.2);">
              <label class="text-white font-weight-bold mb-2"><i class="fas fa-camera mr-2 text-primary"></i> Profile
                Photo</label>
              <input type="file" name="rc" class="text-white-50 small d-block" required accept=".jpg,.jpeg,.png">
              <small class="text-muted mt-1 d-block">JPG or PNG (Max 2MB)</small>
            </div>
          </div>
        </div>

        <div class="text-center">
          <button type="submit" name="submit" class="btn btn-premium btn-gradient px-5 py-3">Register as Driver</button>
          <p class="mt-4 text-white-50">Already have a driver account? <a href="login.php"
              class="text-primary font-weight-bold">Login here</a></p>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .premium-form .form-control:focus {
    background: rgba(255, 255, 255, 0.05) !important;
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 10px rgba(37, 99, 235, 0.2);
    color: white !important;
  }

  .premium-form label {
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-size: 0.75rem;
  }
</style>

<?php include 'templates/footer.php'; ?>