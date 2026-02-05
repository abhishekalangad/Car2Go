<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Redirect if already logged in
if (is_logged_in()) {
  $user_type = $_SESSION['l_type'] ?? '';
  $redirect_map = [
    'admin' => 'adminprofile.php',
    'user' => 'userprofile.php',
    'driver' => 'driverprofile.php',
    'service center' => 'serviceprofile.php',
    'employe' => 'employee/empprofile.php'
  ];
  $redirect_url = $redirect_map[$user_type] ?? 'index.php';
  header("Location: $redirect_url");
  exit();
}

$error_message = '';

if (isset($_POST['login'])) {
  // Verify CSRF token
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Invalid request. Please try again.';
  } else {
    // Sanitize inputs
    $l_uname = sanitize_input($_POST['l_uname']);
    $l_password = $_POST['l_password'];

    // Validate inputs
    if (empty($l_uname) || empty($l_password)) {
      $error_message = 'Please enter both email and password.';
    } else {
      // Use prepared statement to prevent SQL injection
      $query = "SELECT l_id, l_password, l_type, l_approve FROM login WHERE l_uname = ?";
      $user = db_fetch_one($con, $query, "s", [$l_uname]);

      if ($user) {
        // Check if password is hashed or plain text
        $password_valid = false;

        if (strlen($user['l_password']) >= 60) {
          // Hashed password - verify using bcrypt
          $password_valid = verify_password($l_password, $user['l_password']);
        } else {
          // Plain text password (legacy) - direct comparison
          // TODO: Migrate to hashed passwords
          $password_valid = ($l_password === $user['l_password']);

          // If login successful, hash the password for future
          if ($password_valid) {
            $hashed = hash_password($l_password);
            $update = "UPDATE login SET l_password = ? WHERE l_id = ?";
            db_execute($con, $update, "si", [$hashed, $user['l_id']]);
          }
        }

        if ($password_valid) {
          // Check approval status
          if ($user['l_approve'] === 'approve') {
            // Set session variables
            $_SESSION['l_id'] = $user['l_id'];
            $_SESSION['l_type'] = $user['l_type'];
            $_SESSION['l_email'] = $l_uname;

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Redirect based on user type
            $redirect_map = [
              'admin' => 'adminprofile.php',
              'user' => 'userprofile.php',
              'driver' => 'driverprofile.php',
              'service center' => 'serviceprofile.php',
              'employe' => 'employee/empprofile.php'
            ];

            $redirect_url = $redirect_map[$user['l_type']] ?? 'index.php';
            echo "<script>window.location.replace('$redirect_url');</script>";
            exit();
          } else {
            $error_message = 'Please wait until admin approves your account!';
          }
        } else {
          $error_message = 'Username and password mismatch!';
          sleep(1); // Prevent brute force
        }
      } else {
        $error_message = 'Username and password mismatch!';
        sleep(1); // Prevent brute force
      }
    }
  }
}

include('header.php');
?>

<br><br>
<center>
  <div class="banner-form-agileinfo" style="width: 400px; height: 450px;">
    <br><br><br>
    <form action="#" method="post">
      <!-- CSRF Token -->
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

      <input type="text" class="tel" name="l_uname" placeholder="email" required=""
        value="<?php echo isset($_POST['l_uname']) ? e($_POST['l_uname']) : ''; ?>">
      <input type="password" class="tel" name="l_password" placeholder="password" required="">
      <input type="submit" class="hvr-shutter-in-vertical" name="login" value="Login">

      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"
          style="background-color:#ffcccc; color:#cc0000; padding:10px; margin-top:10px; border-radius:5px;">
          <?php echo e($error_message); ?>
        </div>
      <?php endif; ?>

      <?php echo display_flash_message(); ?>
    </form>
  </div>
</center>
<br><br><br>
<br><br><br>

<?php include 'footer.php'; ?>