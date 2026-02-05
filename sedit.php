<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();
require_role('service center');

$l_id = $_SESSION['l_id'];

// Fetch current service center data
$service = db_fetch_one($con, "SELECT * FROM service_reg WHERE sl_id = ?", "i", [$l_id]);

if (!$service) {
  redirect_with_message('serviceprofile.php', 'Profile not found.', 'danger');
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

    // Handle license update
    $license = $service['s_licence'];
    if (!empty($_FILES['license']['name'])) {
      $check = validate_file_upload($_FILES['license'], ['image/jpeg', 'image/png', 'application/pdf'], 3 * 1024 * 1024);
      if ($check === true) {
        $license = generate_unique_filename($_FILES['license']['name']);
        move_uploaded_file($_FILES['license']['tmp_name'], 'uploads/documents/' . $license);
      } else {
        $error_message = "License upload error: " . $check;
      }
    }

    // Handle center photo update
    $photo = $service['s_rc'];
    if (!empty($_FILES['photo']['name'])) {
      $check = validate_file_upload($_FILES['photo'], ['image/jpeg', 'image/png'], 2 * 1024 * 1024);
      if ($check === true) {
        $photo = generate_unique_filename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/services/' . $photo);
      } else {
        $error_message = "Center photo upload error: " . $check;
      }
    }

    if (empty($error_message)) {
      $query = "UPDATE service_reg SET s_name=?, s_email=?, s_address=?, s_pincode=?, s_phone=?, s_licence=?, s_rc=? WHERE sl_id=?";
      if (db_execute($con, $query, "sssssssi", [$name, $email, $address, $pincode, $phone, $license, $photo, $l_id])) {
        db_execute($con, "UPDATE login SET l_uname=? WHERE l_id=?", "si", [$email, $l_id]);
        redirect_with_message('serviceprofile.php', 'Facility profile updated successfully!', 'success');
      } else {
        $error_message = 'Failed to update profile. Email might be in use.';
      }
    }
  }
}

$page_title = 'Manage Service Facility - CAR2GO';
include 'templates/header.php';
?>

<div class="profile-hero py-5" style="background: #1e1b4b; margin-top: -20px;">
  <div class="container text-center text-white">
    <h2 class="font-weight-bold mb-0">Partner Facility Management</h2>
    <p class="opacity-7">Update your service center information for CAR2GO users.</p>
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
                <label class="small font-weight-bold text-muted uppercase">Center Name</label>
                <input type="text" name="name" class="form-control bg-light border-0"
                  value="<?php echo e($service['s_name']); ?>" required>
              </div>
              <div class="col-md-6 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">Business Email</label>
                <input type="email" name="email" class="form-control bg-light border-0"
                  value="<?php echo e($service['s_email']); ?>" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">Contact Phone</label>
                <input type="text" name="phone" class="form-control bg-light border-0"
                  value="<?php echo e($service['s_phone']); ?>" pattern="[0-9]{10}" required>
              </div>
              <div class="col-md-6 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">Operating Pincode</label>
                <input type="text" name="pincode" class="form-control bg-light border-0"
                  value="<?php echo e($service['s_pincode']); ?>" maxlength="6" pattern="[0-9]{6}" required>
              </div>
            </div>

            <div class="form-group mb-4">
              <label class="small font-weight-bold text-muted uppercase">Full Business Address</label>
              <textarea name="address" class="form-control bg-light border-0" rows="3"
                required><?php echo e($service['s_address']); ?></textarea>
            </div>

            <div class="row mb-5">
              <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-muted uppercase">Center Main Photo</label>
                <div class="d-flex align-items-center p-3 border rounded">
                  <img src="uploads/services/<?php echo e($service['s_rc']); ?>" class="rounded mr-3"
                    style="width: 60px; height: 40px; object-fit: cover; background: #eee;">
                  <input type="file" name="photo" class="small w-100">
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-muted uppercase">Business License</label>
                <div class="d-flex align-items-center p-3 border rounded">
                  <i class="fas fa-file-invoice fa-2x text-primary mr-3"></i>
                  <input type="file" name="license" class="small w-100">
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-between">
              <a href="serviceprofile.php" class="btn btn-light px-5 font-weight-bold rounded-pill">Cancel</a>
              <button type="submit" name="submit" class="btn btn-primary px-5 font-weight-bold rounded-pill shadow">Save
                Changes</button>
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

<?php include 'templates/header.php'; ?>