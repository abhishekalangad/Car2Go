<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();
require_role('service center');

$l_id = $_SESSION['l_id'];
$error_message = '';
$success_message = '';

if (isset($_POST['submit'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Invalid request.';
  } else {
    $se_name = sanitize_input($_POST['sename']);
    $se_details = sanitize_input($_POST['sedetails']);
    $se_price = sanitize_input($_POST['seprice']);

    $q = "INSERT INTO service_details (sel_id, se_name, se_details, se_price) VALUES (?, ?, ?, ?)";
    if (db_execute($con, $q, "isss", [$l_id, $se_name, $se_details, $se_price])) {
      $success_message = 'Service successfully added to your facility profile.';
    } else {
      $error_message = 'Failed to add service. Please check your inputs.';
    }
  }
}

$page_title = 'Add New Service Offering - CAR2GO';
include 'templates/header.php';
?>

<div class="profile-hero py-5" style="background: #1e1b4b; margin-top: -20px;">
  <div class="container text-center text-white">
    <h2 class="font-weight-bold mb-0">Expand Your Service Menu</h2>
    <p class="opacity-7">List new maintenance or repair packages for customers.</p>
  </div>
</div>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
        <div class="card-body p-5">
          <?php if ($error_message): ?>
            <div class="alert alert-danger mb-4"><?php echo e($error_message); ?></div>
          <?php endif; ?>

          <?php if ($success_message): ?>
            <div class="alert alert-success mb-4 text-center">
              <i class="fas fa-check-circle fa-2x d-block mb-3"></i>
              <?php echo e($success_message); ?><br>
              <a href="serviceprofile.php" class="btn btn-sm btn-outline-success mt-3 rounded-pill px-4">Return to
                Profile</a>
            </div>
          <?php endif; ?>

          <form action="" method="post" class="premium-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group mb-4">
              <label class="small font-weight-bold text-muted uppercase">Service Title</label>
              <input type="text" name="sename" class="form-control bg-light border-0 py-4"
                placeholder="e.g. Premium Engine Oil Change" required>
            </div>

            <div class="form-group mb-4">
              <label class="small font-weight-bold text-muted uppercase">Service Description</label>
              <textarea name="sedetails" class="form-control bg-light border-0" rows="4"
                placeholder="Describe what's included in this service..." required></textarea>
            </div>

            <div class="form-group mb-5">
              <label class="small font-weight-bold text-muted uppercase">Service Fee (₹)</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-light border-0 text-muted">₹</span>
                </div>
                <input type="number" name="seprice" class="form-control bg-light border-0 py-4" placeholder="0.00"
                  required>
              </div>
              <small class="text-muted">Standard transparent pricing for customers.</small>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <a href="serviceprofile.php" class="text-muted font-weight-bold"><i class="fas fa-arrow-left mr-1"></i>
                Back</a>
              <button type="submit" name="submit" class="btn btn-primary px-5 font-weight-bold rounded-pill shadow">
                PUBLISH SERVICE
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="mt-4 text-center p-4 bg-white rounded-xl shadow-sm border border-indigo-light">
        <h6 class="font-weight-bold text-indigo"><i class="fas fa-lightbulb mr-2"></i> Partner Tip</h6>
        <p class="small text-muted mb-0">Detailed descriptions help users choose your center over others. Mention parts
          brands or specific guarantees.</p>
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

  .text-indigo {
    color: #4338ca;
  }

  .border-indigo-light {
    border-color: #e0e7ff !important;
  }
</style>

<?php include 'templates/header.php'; ?>