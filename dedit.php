<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();
require_role('driver');

$l_id = $_SESSION['l_id'];

// Fetch current driver data
$driver = db_fetch_one($con, "SELECT * FROM driver_reg WHERE dl_id = ?", "i", [$l_id]);

if (!$driver) {
  redirect_with_message('driverprofile.php', 'Profile not found.', 'danger');
}

$error_message = '';

if (isset($_POST['submit'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Invalid request.';
  } else {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $address = sanitize_input($_POST['address']);
    $pincode = sanitize_input($_POST['pincode']);
    $phone = sanitize_input($_POST['phone']);
    $amount = sanitize_input($_POST['amount']);

    // Handle license update
    $license = $driver['d_licence'];
    if (!empty($_FILES['license']['name'])) {
      $check = validate_file_upload($_FILES['license'], ['image/jpeg', 'image/png', 'application/pdf'], 3 * 1024 * 1024);
      if ($check === true) {
        $license = generate_unique_filename($_FILES['license']['name']);
        move_uploaded_file($_FILES['license']['tmp_name'], 'uploads/documents/' . $license);
      } else {
        $error_message = "License upload error: " . $check;
      }
    }

    // Handle profile photo update
    $photo = $driver['d_proof'];
    if (!empty($_FILES['photo']['name'])) {
      $check = validate_file_upload($_FILES['photo'], ['image/jpeg', 'image/png'], 2 * 1024 * 1024);
      if ($check === true) {
        $photo = generate_unique_filename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/drivers/' . $photo);
      } else {
        $error_message = "Photo upload error: " . $check;
      }
    }

    if (empty($error_message)) {
      $query = "UPDATE driver_reg SET d_name=?, d_email=?, d_address=?, d_pincode=?, d_phone=?, d_licence=?, d_proof=?, d_amount=? WHERE dl_id=?";
      if (db_execute($con, $query, "ssssssssi", [$name, $email, $address, $pincode, $phone, $license, $photo, $amount, $l_id])) {
        db_execute($con, "UPDATE login SET l_uname=? WHERE l_id=?", "si", [$email, $l_id]);
        redirect_with_message('driverprofile.php', 'Profile updated successfully!', 'success');
      } else {
        $error_message = 'Failed to update profile. Email might be in use.';
      }
    }
  }
}

$page_title = 'Manage My Partner Profile - CAR2GO';
include 'templates/header.php';
?>

<div class="profile-hero py-5" style="background: #0f172a; margin-top: -20px;">
  <div class="container text-center text-white">
    <h2 class="font-weight-bold mb-0">Partner Professional Profile</h2>
    <p class="opacity-7">Manage your public information and service details.</p>
  </div>
</div>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-9">
      <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
        <div class="card-body p-5">
          <?php if ($error_message): ?>
            <div class="alert alert-danger mb-4"><?php echo e($error_message); ?></div>
          <?php endif; ?>

          <form action="" method="post" enctype="multipart/form-data" class="premium-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="row">
              <div class="col-md-6 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">Full Name</label>
                <input type="text" name="name" class="form-control bg-light border-0"
                  value="<?php echo e($driver['d_name']); ?>" required>
              </div>
              <div class="col-md-6 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">Public Email</label>
                <input type="email" name="email" class="form-control bg-light border-0"
                  value="<?php echo e($driver['d_email']); ?>" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">Phone Number</label>
                <input type="text" name="phone" class="form-control bg-light border-0"
                  value="<?php echo e($driver['d_phone']); ?>" pattern="[0-9]{10}" required>
              </div>
              <div class="col-md-6 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">Daily Service Charge (₹)</label>
                <input type="number" name="amount" class="form-control bg-light border-0"
                  value="<?php echo e($driver['d_amount']); ?>" required>
              </div>
            </div>

            <div class="form-group mb-4">
              <label class="small font-weight-bold text-muted uppercase">Professional Address</label>
              <textarea name="address" class="form-control bg-light border-0" rows="3"
                required><?php echo e($driver['d_address']); ?></textarea>
            </div>

            <div class="form-group mb-4">
              <label class="small font-weight-bold text-muted uppercase">Operating Pincode</label>
              <input type="text" name="pincode" class="form-control bg-light border-0"
                value="<?php echo e($driver['d_pincode']); ?>" maxlength="6" pattern="[0-9]{6}" required>
            </div>

            <div class="row mb-5">
              <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-muted uppercase">Profile Photo</label>
                <div class="d-flex align-items-center p-3 border rounded">
                  <img src="uploads/drivers/<?php echo e($driver['d_proof']); ?>" class="rounded-circle mr-3"
                    style="width: 50px; height: 50px; object-fit: cover;">
                  <input type="file" name="photo" class="small w-100">
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-muted uppercase">Driving License</label>
                <div class="d-flex align-items-center p-3 border rounded">
                  <i class="fas fa-file-contract fa-2x text-primary mr-3"></i>
                  <input type="file" name="license" class="small w-100">
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-between">
              <a href="driverprofile.php" class="btn btn-light px-5 font-weight-bold rounded-pill">Cancel</a>
              <button type="submit" name="submit"
                class="btn btn-primary px-5 font-weight-bold rounded-pill shadow">Confirm Updates</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .rounded-xl {
    border-radius: 20px;
  }

  .uppercase {
    text-transform: uppercase;
    letter-spacing: 1px;
  }
</style>

<?php include 'templates/footer.php'; ?>