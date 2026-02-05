<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

$success_message = '';
$error_message = '';

if (isset($_POST['submit'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Invalid request. Please try again.';
  } else {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $address = sanitize_input($_POST['address']);
    $pincode = sanitize_input($_POST['pincode']);
    $phone = sanitize_input($_POST['phone']);

    if (!validate_email($email)) {
      $error_message = 'Invalid email address';
    } else if (!validate_password($password)) {
      $error_message = 'Password must be 8-15 characters with uppercase, lowercase, and number';
    } else if (!validate_phone($phone)) {
      $error_message = 'Invalid phone number';
    } else {
      $licence_valid = validate_file_upload($_FILES['licence'], ['image/jpeg', 'image/png', 'application/pdf'], 2 * 1024 * 1024);
      $rc_valid = validate_file_upload($_FILES['rc'], ['image/jpeg', 'image/png'], 2 * 1024 * 1024);

      if ($licence_valid !== true) {
        $error_message = "Document error: " . $licence_valid;
      } else if ($rc_valid !== true) {
        $error_message = "RC/Proof error: " . $rc_valid;
      } else {
        $check_query = "SELECT l_id FROM login WHERE l_uname = ?";
        if (db_fetch_one($con, $check_query, "s", [$email])) {
          $error_message = 'Email address already in use';
        } else {
          $hashed_password = hash_password($password);
          $type = 'service center';
          $approve = 'not approve';

          $query1 = "INSERT INTO login (l_uname, l_password, l_type, l_approve) VALUES (?, ?, ?, ?)";
          if (db_execute($con, $query1, "ssss", [$email, $hashed_password, $type, $approve])) {
            $id = $con->insert_id;

            $licence_filename = generate_unique_filename($_FILES['licence']['name']);
            $rc_filename = generate_unique_filename($_FILES['rc']['name']);

            $query2 = "INSERT INTO service_reg (sl_id, s_name, s_email, s_password, s_address, s_pincode, s_phone, s_licence, s_rc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            if (db_execute($con, $query2, "issssssss", [$id, $name, $email, $hashed_password, $address, $pincode, $phone, $licence_filename, $rc_filename])) {

              $doc_dir = "uploads/documents/";
              $service_dir = "uploads/services/";

              if (!is_dir($doc_dir))
                mkdir($doc_dir, 0755, true);
              if (!is_dir($service_dir))
                mkdir($service_dir, 0755, true);

              $file1 = move_uploaded_file($_FILES['licence']['tmp_name'], $doc_dir . $licence_filename);
              $file2 = move_uploaded_file($_FILES['rc']['tmp_name'], $service_dir . $rc_filename);

              if ($file1 && $file2) {
                redirect_with_message('login.php', 'Service Center registration successful! Pending admin approval.', 'success');
                exit();
              } else {
                $error_message = 'File upload failed.';
              }
            } else {
              $error_message = 'Profile creation failed.';
            }
          } else {
            $error_message = 'Account creation failed.';
          }
        }
      }
    }
  }
}

$page_title = 'Service Center Registration - CAR2GO';
include 'templates/header.php';
?>

<div class="hero-section" style="height: auto; padding: 100px 0;">
  <div class="container d-flex justify-content-center">
    <div class="glass-card" style="width: 100%; max-width: 800px; padding: 40px;">
      <div class="text-center mb-5">
        <h2 class="display-4 font-weight-bold" style="color: white; letter-spacing: -2px;">Partner <span>Service
            Center</span></h2>
        <p class="text-white-50">Join our network of authorized service providers.</p>
      </div>

      <?php if ($error_message): ?>
        <div class="alert alert-danger mb-4"><?php echo e($error_message); ?></div>
      <?php endif; ?>

      <form action="#" method="post" enctype="multipart/form-data" class="premium-form">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Center Name</label>
            <input type="text" class="form-control bg-transparent text-white border-secondary" name="name"
              placeholder="Service center name" required
              value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Business Email</label>
            <input type="email" class="form-control bg-transparent text-white border-secondary" name="email"
              placeholder="official@business.com" required
              value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Create Password</label>
            <input type="password" class="form-control bg-transparent text-white border-secondary" name="password"
              placeholder="Min. 8 characters" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Contact Phone</label>
            <input type="text" class="form-control bg-transparent text-white border-secondary" name="phone"
              placeholder="10-digit number" required pattern="[0-9]{10}" maxlength="10"
              value="<?php echo isset($_POST['phone']) ? e($_POST['phone']) : ''; ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="text-white small mb-1">Full Business Address</label>
          <textarea name="address" class="form-control bg-transparent text-white border-secondary" rows="3"
            placeholder="Shop/Building No, Street, Locality"
            required><?php echo isset($_POST['address']) ? e($_POST['address']) : ''; ?></textarea>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="text-white small mb-1">Pincode</label>
            <input type="text" class="form-control bg-transparent text-white border-secondary" name="pincode"
              placeholder="6-digit" required maxlength="6"
              value="<?php echo isset($_POST['pincode']) ? e($_POST['pincode']) : ''; ?>">
          </div>
        </div>

        <div class="row mb-5 mt-4">
          <div class="col-md-6 mb-3">
            <div class="p-3 border border-secondary rounded" style="background: rgba(0,0,0,0.2);">
              <label class="text-white font-weight-bold mb-2"><i class="fas fa-file-invoice mr-2 text-primary"></i>
                Business License</label>
              <input type="file" name="licence" class="text-white-50 small d-block" required
                accept=".jpg,.jpeg,.png,.pdf">
              <small class="text-muted mt-1 d-block">Official permit document</small>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="p-3 border border-secondary rounded" style="background: rgba(0,0,0,0.2);">
              <label class="text-white font-weight-bold mb-2"><i class="fas fa-image mr-2 text-primary"></i> Center
                Photo</label>
              <input type="file" name="rc" class="text-white-50 small d-block" required accept=".jpg,.jpeg,.png">
              <small class="text-muted mt-1 d-block">Main entrance or billboard photo</small>
            </div>
          </div>
        </div>

        <div class="text-center">
          <button type="submit" name="submit" class="btn btn-premium btn-gradient px-5 py-3">Register Service
            Center</button>
          <p class="mt-4 text-white-50">Already partnered? <a href="login.php"
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
</style>

<?php include 'templates/footer.php'; ?>