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
    background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), url('images/bg8.jpg') center/cover;
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
    border-color: #4338ca;
    outline: none;
    box-shadow: 0 0 0 4px rgba(67, 56, 202, 0.1);
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
    border-color: #4338ca;
    background: #e0e7ff;
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
    color: #e0e7ff;
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
          <span class="badge badge-light text-primary px-3 py-1 rounded-pill font-weight-bold mb-3">SERVICE
            PARTNER</span>
          <h2 class="font-weight-bold display-5 mb-4">Grow Your<br>Business.</h2>
          <p class="lead opacity-8">Connect with thousands of car owners. Streamline your bookings and payments.</p>
        </div>

        <div class="benefits-list">
          <div class="benefit-item">
            <div class="benefit-icon"><i class="fas fa-users"></i></div>
            <div>
              <h6 class="font-weight-bold mb-0 text-white">More Customers</h6>
              <small class="opacity-7">Instant exposure to clients</small>
            </div>
          </div>
          <div class="benefit-item">
            <div class="benefit-icon"><i class="fas fa-chart-line"></i></div>
            <div>
              <h6 class="font-weight-bold mb-0 text-white">Digital Tools</h6>
              <small class="opacity-7">Manage jobs efficiently</small>
            </div>
          </div>
          <div class="benefit-item">
            <div class="benefit-icon"><i class="fas fa-check-circle"></i></div>
            <div>
              <h6 class="font-weight-bold mb-0 text-white">Verified Partner</h6>
              <small class="opacity-7">Build trust with the Pro badge</small>
            </div>
          </div>
        </div>

        <div class="mt-auto">
          <p class="small opacity-5 mb-0">&copy; 2026 CAR2GO Service Network</p>
        </div>
      </div>
    </div>

    <!-- Right: Registration Form -->
    <div class="form-wrapper">
      <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="font-weight-bold text-dark mb-0">Biz Registration</h3>
        <a href="login.php" class="small font-weight-bold text-primary">Already a partner?</a>
      </div>

      <?php if ($error_message): ?>
        <div class="alert alert-danger rounded-lg mb-4"><?php echo e($error_message); ?></div>
      <?php endif; ?>

      <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <div class="row">
          <div class="col-md-6 form-group-custom">
            <label class="form-label-custom">Center Name</label>
            <input type="text" name="name" class="form-control-custom" placeholder="e.g. Apex Auto Care" required
              value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>">
          </div>
          <div class="col-md-6 form-group-custom">
            <label class="form-label-custom">Email Address</label>
            <input type="email" name="email" class="form-control-custom" placeholder="biz@example.com" required
              value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 form-group-custom">
            <label class="form-label-custom">Phone Number</label>
            <input type="text" name="phone" class="form-control-custom" placeholder="10-digit mobile" required
              pattern="[0-9]{10}" maxlength="10"
              value="<?php echo isset($_POST['phone']) ? e($_POST['phone']) : ''; ?>">
          </div>
          <div class="col-md-6 form-group-custom">
            <label class="form-label-custom">Password</label>
            <input type="password" name="password" class="form-control-custom" placeholder="Min. 8 chars" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-8 form-group-custom">
            <label class="form-label-custom">Business Address</label>
            <input type="text" name="address" class="form-control-custom" placeholder="Shop No, Street, Landmark"
              required value="<?php echo isset($_POST['address']) ? e($_POST['address']) : ''; ?>">
          </div>
          <div class="col-md-4 form-group-custom">
            <label class="form-label-custom">Pincode</label>
            <input type="text" name="pincode" class="form-control-custom" placeholder="6-digit" required maxlength="6"
              value="<?php echo isset($_POST['pincode']) ? e($_POST['pincode']) : ''; ?>">
          </div>
        </div>

        <h6 class="font-weight-bold text-dark mt-4 mb-3">Business Verification</h6>
        <div class="row">
          <div class="col-md-6 mb-3">
            <div class="upload-box position-relative">
              <input type="file" name="licence" id="licence" class="position-absolute w-100 h-100"
                style="opacity:0; top:0; left:0; cursor:pointer;" required>
              <i class="fas fa-file-contract fa-2x text-muted mb-2"></i>
              <h6 class="font-weight-bold text-dark mb-1">Business License</h6>
              <small class="text-muted d-block">Registration Cert (PDF/JPG)</small>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="upload-box position-relative">
              <input type="file" name="rc" id="rc" class="position-absolute w-100 h-100"
                style="opacity:0; top:0; left:0; cursor:pointer;" required>
              <i class="fas fa-store fa-2x text-muted mb-2"></i>
              <h6 class="font-weight-bold text-dark mb-1">Center Photo</h6>
              <small class="text-muted d-block">Front view photo (JPG)</small>
            </div>
          </div>
        </div>

        <button type="submit" name="submit"
          class="btn btn-primary btn-block py-3 mt-4 font-weight-bold shadow-sm rounded-pill"
          style="background: linear-gradient(135deg, #4338ca, #3730a3); border:none;">
          REGISTER PARTNER <i class="fas fa-arrow-right ml-2"></i>
        </button>
      </form>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>