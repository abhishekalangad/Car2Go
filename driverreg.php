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
    min-height: 85vh;
  }

  .register-visual {
    flex: 1;
    background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), url('images/bg7.jpg') center/cover;
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
    flex: 1.4;
    padding: 4rem;
    overflow-y: auto;
    /* max-height: 85vh; */
  }

  .form-group-custom {
    margin-bottom: 1.5rem;
  }

  .form-label-custom {
    display: block;
    font-weight: 600;
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .form-control-custom {
    width: 100%;
    padding: 0.75rem 1rem;
    background: #f1f5f9;
    border: 2px solid transparent;
    border-radius: 12px;
    color: #1e293b;
    transition: all 0.2s;
    font-weight: 500;
  }

  .form-control-custom:focus {
    background: white;
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  }

  .upload-box {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    transition: 0.3s;
    cursor: pointer;
  }

  .upload-box:hover {
    border-color: #3b82f6;
    background: #eff6ff;
  }

  .benefit-item {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
  }

  .benefit-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: #eff6ff;
  }

  @media (max-width: 991px) {
    .register-container {
      flex-direction: column;
    }

    .register-visual {
      padding: 3rem;
    }

    .form-wrapper {
      padding: 2rem;
    }
  }
</style>

<div class="container-fluid p-0">
  <div class="register-container">
    <!-- Left: Visual Side -->
    <div class="register-visual">
      <div class="register-content">
        <div class="mb-5">
          <span class="badge badge-light text-primary px-3 py-1 rounded-pill font-weight-bold mb-3">DRIVER
            PARTNER</span>
          <h2 class="font-weight-bold display-5 mb-4">Drive Your<br>Own Career.</h2>
          <p class="lead opacity-8">Join the elite CAR2GO fleet. Enjoy flexible hours, premium clients, and guaranteed
            payments.</p>
        </div>

        <div class="benefits-list">
          <div class="benefit-item">
            <div class="benefit-icon"><i class="fas fa-wallet"></i></div>
            <div>
              <h6 class="font-weight-bold mb-0 text-white">High Earnings</h6>
              <small class="opacity-7">Competitive rates + tips</small>
            </div>
          </div>
          <div class="benefit-item">
            <div class="benefit-icon"><i class="fas fa-calendar-check"></i></div>
            <div>
              <h6 class="font-weight-bold mb-0 text-white">Flexible Schedule</h6>
              <small class="opacity-7">Be your own boss</small>
            </div>
          </div>
          <div class="benefit-item">
            <div class="benefit-icon"><i class="fas fa-shield-alt"></i></div>
            <div>
              <h6 class="font-weight-bold mb-0 text-white">Secure Payments</h6>
              <small class="opacity-7">Weekly direct deposits</small>
            </div>
          </div>
        </div>

        <div class="mt-auto">
          <p class="small opacity-5 mb-0">&copy; 2026 CAR2GO Driver Network</p>
        </div>
      </div>
    </div>

    <!-- Right: Registration Form -->
    <div class="form-wrapper">
      <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="font-weight-bold text-dark mb-0">Create Profile</h3>
        <a href="login.php" class="small font-weight-bold text-primary">Already registered?</a>
      </div>

      <?php if ($error_message): ?>
        <div class="alert alert-danger rounded-lg mb-4"><?php echo e($error_message); ?></div>
      <?php endif; ?>

      <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <div class="row">
          <div class="col-md-6 form-group-custom">
            <label class="form-label-custom">Full Name</label>
            <input type="text" name="name" class="form-control-custom" placeholder="e.g. Rahul Kumar" required
              value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>">
          </div>
          <div class="col-md-6 form-group-custom">
            <label class="form-label-custom">Phone Number</label>
            <input type="text" name="phone" class="form-control-custom" placeholder="10-digit mobile" required
              pattern="[0-9]{10}" maxlength="10"
              value="<?php echo isset($_POST['phone']) ? e($_POST['phone']) : ''; ?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 form-group-custom">
            <label class="form-label-custom">Email Address</label>
            <input type="email" name="email" class="form-control-custom" placeholder="john@example.com" required
              value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>">
          </div>
          <div class="col-md-6 form-group-custom">
            <label class="form-label-custom">Password</label>
            <input type="password" name="password" class="form-control-custom" placeholder="Min. 8 chars" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-8 form-group-custom">
            <label class="form-label-custom">Address</label>
            <input type="text" name="address" class="form-control-custom" placeholder="House/Flat No, Street, Area"
              required value="<?php echo isset($_POST['address']) ? e($_POST['address']) : ''; ?>">
          </div>
          <div class="col-md-4 form-group-custom">
            <label class="form-label-custom">Pincode</label>
            <input type="text" name="pincode" class="form-control-custom" placeholder="6-digit" required maxlength="6"
              value="<?php echo isset($_POST['pincode']) ? e($_POST['pincode']) : ''; ?>">
          </div>
        </div>

        <div class="form-group-custom">
          <label class="form-label-custom">Expected Daily Rate (₹)</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text bg-light border-0">₹</span>
            </div>
            <input type="number" name="d_amt" class="form-control-custom" placeholder="e.g. 800" required
              value="<?php echo isset($_POST['d_amt']) ? e($_POST['d_amt']) : ''; ?>">
          </div>
        </div>

        <h6 class="font-weight-bold text-dark mt-4 mb-3">Verification Documents</h6>
        <div class="row">
          <div class="col-md-6 mb-3">
            <div class="upload-box position-relative">
              <input type="file" name="licence" id="licence" class="position-absolute w-100 h-100"
                style="opacity:0; top:0; left:0; cursor:pointer;" required>
              <i class="fas fa-id-card fa-2x text-muted mb-2"></i>
              <h6 class="font-weight-bold text-dark mb-1">Driving Licence</h6>
              <small class="text-muted d-block">Upload front side (JPG/PDF)</small>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="upload-box position-relative">
              <input type="file" name="rc" id="rc" class="position-absolute w-100 h-100"
                style="opacity:0; top:0; left:0; cursor:pointer;" required>
              <i class="fas fa-user-circle fa-2x text-muted mb-2"></i>
              <h6 class="font-weight-bold text-dark mb-1">Profile Photo</h6>
              <small class="text-muted d-block">Professional headshot (JPG)</small>
            </div>
          </div>
        </div>

        <button type="submit" name="submit"
          class="btn btn-primary btn-block py-3 mt-4 font-weight-bold shadow-sm rounded-pill"
          style="background: linear-gradient(135deg, #3b82f6, #2563eb); border:none;">
          COMPLETE REGISTRATION <i class="fas fa-arrow-right ml-2"></i>
        </button>
      </form>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>