<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();
require_role('user');

$l_id = $_SESSION['l_id'];

// Fetch current user data
$user = db_fetch_one($con, "SELECT * FROM user_reg WHERE ul_id = ?", "i", [$l_id]);

if (!$user) {
  redirect_with_message('userprofile.php', 'Profile not found.', 'danger');
}

$error_message = '';
$success_message = '';

if (isset($_POST['submit'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Invalid request.';
  } else {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $address = sanitize_input($_POST['address']);
    $pincode = sanitize_input($_POST['pincode']);
    $phone = sanitize_input($_POST['phone']);

    // Handle file upload
    $image = $user['u_licence'];
    if (!empty($_FILES['image']['name'])) {
      $check = validate_file_upload($_FILES['image'], ['image/jpeg', 'image/png', 'application/pdf'], 5 * 1024 * 1024);
      if ($check === true) {
        $image = generate_unique_filename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/documents/' . $image);
      } else {
        $error_message = "File upload error: " . $check;
      }
    }

    if (empty($error_message)) {
      $query = "UPDATE user_reg SET u_name=?, u_email=?, u_address=?, u_pincode=?, u_phone=?, u_licence=? WHERE ul_id=?";
      if (db_execute($con, $query, "ssssssi", [$name, $email, $address, $pincode, $phone, $image, $l_id])) {
        db_execute($con, "UPDATE login SET l_uname=? WHERE l_id=?", "si", [$email, $l_id]);
        redirect_with_message('userprofile.php', 'Profile updated successfully!', 'success');
      } else {
        $error_message = 'Failed to update profile. Email might be in use.';
      }
    }
  }
}

$page_title = 'Edit My Profile - CAR2GO';
include 'templates/header.php';
?>

<div class="profile-hero py-5" style="background: var(--bg-dark); margin-top: -20px;">
  <div class="container text-center">
    <h2 class="text-white font-weight-bold mb-0">Update Your Information</h2>
    <p class="text-white-50">Keep your profile details up to date for better services.</p>
  </div>
</div>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
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
                  value="<?php echo e($user['u_name']); ?>" required>
              </div>
              <div class="col-md-6 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">Email Address</label>
                <input type="email" name="email" class="form-control bg-light border-0"
                  value="<?php echo e($user['u_email']); ?>" required>
              </div>
            </div>

            <div class="form-group mb-4">
              <label class="small font-weight-bold text-muted uppercase">Residential Address</label>
              <textarea name="address" class="form-control bg-light border-0" rows="3"
                required><?php echo e($user['u_address']); ?></textarea>
            </div>

            <div class="row">
              <div class="col-md-6 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">Pincode</label>
                <input type="text" name="pincode" class="form-control bg-light border-0"
                  value="<?php echo e($user['u_pincode']); ?>" maxlength="6" pattern="[0-9]{6}" required>
              </div>
              <div class="col-md-6 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">Phone Number</label>
                <input type="text" name="phone" class="form-control bg-light border-0"
                  value="<?php echo e($user['u_phone']); ?>" pattern="[0-9]{10}" required>
              </div>
            </div>

            <div class="form-group mb-5">
              <label class="small font-weight-bold text-muted uppercase">Identity Proof (License/ID)</label>
              <div class="d-flex align-items-center p-3 bg-light rounded">
                <a href="uploads/documents/<?php echo e($user['u_licence']); ?>" target="_blank" class="mr-3">
                  <i class="fas fa-file-pdf fa-2x text-primary"></i>
                </a>
                <div class="flex-grow-1">
                  <input type="file" name="image" class="small d-block">
                  <small class="text-muted">Leave empty to keep current document</small>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-between">
              <a href="userprofile.php" class="btn btn-light px-5 font-weight-bold rounded-pill">Cancel</a>
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

<?php include 'templates/footer.php'; ?>