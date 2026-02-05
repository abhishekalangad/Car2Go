<?php
session_start();
require_once 'db_connect.php';
require_once 'config/db_connect.php';
require_once 'includes/security.php';

$success_message = '';
$error_message = '';

if (isset($_POST['submit'])) {
  // Verify CSRF token
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Invalid request. Please try again.';
  } else {
    // Sanitize inputs
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $address = sanitize_input($_POST['address']);
    $pincode = sanitize_input($_POST['pincode']);
    $phone = sanitize_input($_POST['phone']);

    // Validate inputs
    if (!validate_email($email)) {
      $error_message = 'Invalid email address';
    } else if (!validate_password($password)) {
      $error_message = 'Password must be 8-15 characters with uppercase, lowercase, and number';
    } else if (!validate_pincode($pincode)) {
      $error_message = 'Invalid pincode';
    } else if (!validate_phone($phone)) {
      $error_message = 'Invalid phone number';
    } else {
      // Validate file upload
      $file_validation = validate_file_upload($_FILES['licence'], ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'], 5 * 1024 * 1024);

      if ($file_validation !== true) {
        $error_message = $file_validation;
      } else {
        // Hash password BEFORE storing
        $hashed_password = hash_password($password);

        // Use prepared statements to prevent SQL injection
        $type = 'user';
        $approve = 'approve';

        $query1 = "INSERT INTO login (l_uname, l_password, l_type, l_approve) VALUES (?, ?, ?, ?)";
        $stmt1 = db_execute($con, $query1, "ssss", [$email, $hashed_password, $type, $approve]);

        if ($stmt1) {
          $id = $con->insert_id;

          // Generate unique filename for uploaded file
          $licence_filename = generate_unique_filename($_FILES['licence']['name']);

          $query2 = "INSERT INTO user_reg (ul_id, u_name, u_email, u_password, u_address, u_pincode, u_phone, u_licence) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
          $stmt2 = db_execute($con, $query2, "isssssss", [$id, $name, $email, $hashed_password, $address, $pincode, $phone, $licence_filename]);

          if ($stmt2) {
            // Create uploads directory if it doesn't exist
            $upload_dir = "uploads/documents/";
            if (!is_dir($upload_dir)) {
              mkdir($upload_dir, 0755, true);
            }

            $licence_path = $upload_dir . $licence_filename;

            if (move_uploaded_file($_FILES['licence']['tmp_name'], $licence_path)) {
              $success_message = 'Successfully registered! You can now login.';
              // Clear form by redirecting
              redirect_with_message('login.php', 'Registration successful! Please login.', 'success');
            } else {
              $error_message = 'Error uploading file. Registration incomplete.';
            }
          } else {
            $error_message = 'Error creating user profile';
          }
        } else {
          $error_message = 'Email already exists or registration failed';
        }
      }
    }
  }
}

include 'header.php';
?>

<br><br>
<center>
  <div class="banner-form-agileinfo" style="width: 800px; min-height: 700px; padding: 20px;">
    <h5>Fill Out The <span>Details</span></h5><br><br>

    <?php if (!empty($error_message)): ?>
      <div class="alert alert-danger"
        style="background-color:#ffcccc; color:#cc0000; padding:10px; margin:10px; border-radius:5px;">
        <?php echo e($error_message); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
      <div class="alert alert-success"
        style="background-color:#ccffcc; color:#006600; padding:10px; margin:10px; border-radius:5px;">
        <?php echo e($success_message); ?>
      </div>
    <?php endif; ?>

    <form action="#" method="post" enctype="multipart/form-data">
      <!-- CSRF Token -->
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

      <input type="text" class="email" name="name" placeholder="Name" required=""
        value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>">

      <input type="email" class="tel" name="email" placeholder="Email" required=""
        value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>">

      <input type="password" class="tel" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,15}"
        title="Must contain at least one number, one uppercase and lowercase letter, and 8-15 characters"
        placeholder="Password" required="">

      <textarea name="address" cols="3" rows="3"
        placeholder="Address"><?php echo isset($_POST['address']) ? e($_POST['address']) : ''; ?></textarea>

      <input type="text" class="email" name="pincode" placeholder="Pincode" maxlength="6" minlength="6"
        pattern="[0-9]{6}" required="" value="<?php echo isset($_POST['pincode']) ? e($_POST['pincode']) : ''; ?>">

      <input type="text" class="tel" name="phone" placeholder="Phone No" pattern="[0-9]{10}" maxlength="10"
        minlength="10" required="" value="<?php echo isset($_POST['phone']) ? e($_POST['phone']) : ''; ?>">

      <label for="img1" style="color: white;">ID Proof (JPG, PNG, or PDF - Max 5MB)</label>
      <input type="file" id="img1" name="licence" accept=".jpg,.jpeg,.png,.pdf" required
        style="border: none; width: 100%; background: rgba(0, 0, 0, 0.5); padding: 10px 15px; margin-bottom: 15px; outline: none; font-size: 14px; color: #fff; letter-spacing: 1px;">
      <br><br>

      <input type="submit" class="hvr-shutter-in-vertical" value="Get started" name="submit">

      <div style="margin-top:15px; color:white;">
        Already have an account? <a href="login.php" style="color:#4CAF50;">Login here</a>
      </div>
    </form>
  </div>
</center>
<br><br><br>
<br><br><br>
<br><br><br>

<?php include 'footer.php'; ?>