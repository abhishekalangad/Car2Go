<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Ensure only admin
require_role('admin');

$error_message = '';
$success_message = '';

// Handle Form Submission
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
      $error_message = 'Password must be 8-15 chars, alphanumeric.';
    } else if (!validate_pincode($pincode)) {
      $error_message = 'Invalid 6-digit pincode.';
    } else if (!validate_phone($phone)) {
      $error_message = 'Invalid 10-digit phone number.';
    } else {
      // Check if email already exists
      $check_email = db_fetch_one($con, "SELECT l_id FROM login WHERE l_uname = ?", "s", [$email]);
      if ($check_email) {
        $error_message = 'Email address is already registered.';
      } else {
        $hashed_password = hash_password($password);
        $type = 'employe';
        $approve = 'approve';

        if (db_execute($con, "INSERT INTO login (l_uname, l_password, l_type, l_approve) VALUES (?, ?, ?, ?)", "ssss", [$email, $hashed_password, $type, $approve])) {
          $id = $con->insert_id;
          $query = "INSERT INTO emp_reg (el_id, e_name, e_email, e_password, e_address, e_pincode, e_phone) VALUES (?, ?, ?, ?, ?, ?, ?)";
          // Note: Inserting hashed password into e_password too? Legacy did. But we should stick to login table.
          // Legacy code: `INSERT INTO emp_reg ... e_password ...` with plain text.
          // I'll insert hashed password into e_password as well to keep schema consistent, though redundant.

          if (db_execute($con, $query, "issssss", [$id, $name, $email, $hashed_password, $address, $pincode, $phone])) {
            $success_message = 'Employee registered successfully.';
          } else {
            // Cleanup login if reg fails? Simple rollback not avail in basic mysqli without transaction.
            // For now accept minor inconsistency risk or delete login.
            db_execute($con, "DELETE FROM login WHERE l_id = ?", "i", [$id]);
            $error_message = 'Error saving employee details.';
          }
        } else {
          $error_message = 'Registration failed. System error.';
        }
      }
    }
  }
}

$page_title = 'Register Employee - CAR2GO';
include 'templates/header.php';
?>

<div class="page-hero">
  <div class="container hero-content text-center">
    <h1 class="font-weight-bold mb-2 text-white">Register New Employee</h1>
    <p class="lead text-white-50">Create staff accounts securely.</p>
  </div>
</div>

<div class="container animate__animated animate__fadeInUp animate__delay-1s"
  style="margin-top: -80px; position: relative; z-index: 10;">

  <div class="glass-card mb-5"
    style="background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); padding: 40px; max-width: 800px; margin: 0 auto;">

    <?php if ($error_message): ?>
      <div class="alert alert-danger mb-4 rounded-lg shadow-sm border-0">
        <i class="fas fa-exclamation-circle mr-2"></i> <?php echo e($error_message); ?>
      </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
      <div class="alert alert-success mb-4 rounded-lg shadow-sm border-0">
        <i class="fas fa-check-circle mr-2"></i> <?php echo e($success_message); ?>
      </div>
      <div class="text-center mb-4">
        <a href="viewemp.php" class="btn btn-outline-primary rounded-pill px-4">View All Employees</a>
      </div>
    <?php endif; ?>

    <form action="" method="post">
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

      <div class="row">
        <div class="col-md-6 mb-4">
          <label class="font-weight-bold text-muted small text-uppercase">Full Name</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
            </div>
            <input type="text" class="form-control bg-light border-0" name="name" placeholder="John Doe" required>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <label class="font-weight-bold text-muted small text-uppercase">Email Address</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-muted"></i></span>
            </div>
            <input type="email" class="form-control bg-light border-0" name="email" placeholder="john@car2go.com"
              required>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-4">
          <label class="font-weight-bold text-muted small text-uppercase">Password</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
            </div>
            <input type="password" class="form-control bg-light border-0" name="password" placeholder="Secure Password"
              required>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <label class="font-weight-bold text-muted small text-uppercase">Phone Number</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text bg-light border-0"><i class="fas fa-phone text-muted"></i></span>
            </div>
            <input type="tel" class="form-control bg-light border-0" name="phone" placeholder="9876543210"
              pattern="[0-9]{10}" required>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <label class="font-weight-bold text-muted small text-uppercase">Full Address</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-light border-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
          </div>
          <textarea class="form-control bg-light border-0" name="address" rows="2" placeholder="Street, City, State"
            required></textarea>
        </div>
      </div>

      <div class="row mb-5">
        <div class="col-md-6">
          <label class="font-weight-bold text-muted small text-uppercase">Pincode</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text bg-light border-0"><i class="fas fa-map-pin text-muted"></i></span>
            </div>
            <input type="text" class="form-control bg-light border-0" name="pincode" placeholder="123456" maxlength="6"
              required>
          </div>
        </div>
      </div>

      <button type="submit" name="submit"
        class="btn btn-primary btn-block btn-lg rounded-pill font-weight-bold shadow-lg hover-lift">
        Register Employee
      </button>

    </form>
  </div>
</div>

<style>
  .page-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 5rem 0 8rem;
    color: white;
    position: relative;
    overflow: hidden;
  }

  .form-control:focus {
    background-color: white !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  }

  .hover-lift {
    transition: transform 0.2s;
  }

  .hover-lift:hover {
    transform: translateY(-2px);
  }
</style>

<?php include 'templates/footer.php'; ?>