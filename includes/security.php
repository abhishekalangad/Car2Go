<?php
/**
 * Security Functions
 * Functions for input validation, sanitization, and security
 */

/**
 * Sanitize string input
 * Prevents XSS attacks
 * 
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitize_input($data)
{
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }

    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

    return $data;
}

/**
 * Validate email address
 * 
 * @param string $email Email to validate
 * @return bool True if valid
 */
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (10 digits)
 * 
 * @param string $phone Phone number
 * @return bool True if valid
 */
function validate_phone($phone)
{
    return preg_match('/^\d{10}$/', $phone);
}

/**
 * Validate pincode (6 digits)
 * 
 * @param string $pincode Pincode
 * @return bool True if valid
 */
function validate_pincode($pincode)
{
    return preg_match('/^\d{6}$/', $pincode);
}

/**
 * Validate password strength
 * At least 8 characters, 1 uppercase, 1 lowercase, 1 number
 * 
 * @param string $password Password to validate
 * @return bool True if valid
 */
function validate_password($password)
{
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
}

/**
 * Hash password securely
 * 
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hash_password($password)
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password against hash
 * 
 * @param string $password Plain text password
 * @param string $hash Stored hash
 * @return bool True if password matches
 */
function verify_password($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if valid
 */
function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate file upload
 * 
 * @param array $file $_FILES array element
 * @param array $allowed_types Allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @return array ['success' => bool, 'message' => string]
 */
function validate_file_upload($file, $allowed_types = [], $max_size = 5242880)
{
    // Check if file was uploaded
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }

    // Check file size
    if ($file['size'] > $max_size) {
        $max_mb = round($max_size / 1048576, 2);
        return ['success' => false, 'message' => "File size exceeds {$max_mb}MB"];
    }

    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!empty($allowed_types) && !in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }

    // Check if it's actually an image (for image uploads)
    if (strpos($mime_type, 'image/') === 0) {
        $image_info = getimagesize($file['tmp_name']);
        if ($image_info === false) {
            return ['success' => false, 'message' => 'Invalid image file'];
        }
    }

    return ['success' => true, 'message' => 'File is valid'];
}

/**
 * Generate unique filename
 * 
 * @param string $original_name Original filename
 * @return string Unique filename
 */
function generate_unique_filename($original_name)
{
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    return uniqid('upload_', true) . '.' . $extension;
}

/**
 * Redirect with message
 * 
 * @param string $url URL to redirect to
 * @param string $message Message to display
 * @param string $type Message type (success, error, warning, info)
 */
function redirect_with_message($url, $message, $type = 'info')
{
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $url");
    exit();
}

/**
 * Display flash message
 * 
 * @return string HTML for flash message
 */
function display_flash_message()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);

        $class_map = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];

        $class = $class_map[$type] ?? 'alert-info';

        return "<div class='alert $class alert-dismissible fade show' role='alert'>
                    $message
                    <button type='button' class='close' data-dismiss='alert'>&times;</button>
                </div>";
    }

    return '';
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in
 */
function is_logged_in()
{
    return isset($_SESSION['l_id']);
}

/**
 * Check if user has specific role
 * 
 * @param string $role Required role
 * @return bool True if user has role
 */
function has_role($role)
{
    return isset($_SESSION['l_type']) && $_SESSION['l_type'] === $role;
}

/**
 * Require login
 * Redirect to login page if not logged in
 */
function require_login()
{
    if (!is_logged_in()) {
        redirect_with_message('login.php', 'Please login to continue', 'warning');
    }
}

/**
 * Require specific role
 * 
 * @param string $role Required role
 */
function require_role($role)
{
    require_login();

    if (!has_role($role)) {
        redirect_with_message('index.php', 'Access denied', 'error');
    }
}

/**
 * Escape output for HTML
 * 
 * @param string $string String to escape
 * @return string Escaped string
 */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Calculate date difference in days
 * 
 * @param string $date1 First date
 * @param string $date2 Second date
 * @return int Number of days
 */
function date_diff_days($date1, $date2)
{
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval->days;
}
?>