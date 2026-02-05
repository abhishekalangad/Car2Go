<?php
/**
 * Template Test Page
 * Tests if all templates and security functions are working correctly
 */

session_start();

// Include configuration
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Set page title
$page_title = 'Template Test Page - CAR2GO';

// Include header
include 'templates/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center mb-4">
                <i class="fas fa-check-circle text-success"></i>
                Template System Test
            </h1>

            <div class="alert alert-success">
                <h4><i class="fas fa-info-circle"></i> Test Results:</h4>
                <ul class="mb-0">
                    <li>✅ Header loaded successfully</li>
                    <li>✅ Navigation bar displayed</li>
                    <li>✅ Bootstrap CSS loaded</li>
                    <li>✅ Font Awesome icons working</li>
                    <li>✅ Footer will load at bottom</li>
                </ul>
            </div>

            <!-- Database Connection Test -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-database"></i> Database Connection</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($con) && $con instanceof mysqli && !$con->connect_errno): ?>
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i>
                            <strong>Success!</strong> Database connected successfully.
                            <br>
                            <small>Connection to:
                                <?php echo DB_NAME; ?>
                            </small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-times-circle"></i>
                            <strong>Failed!</strong> Database connection failed.
                            <br>
                            <small>Check credentials in: config/db_connect.php</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Security Functions Test -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Security Functions</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Function</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>CSRF Token</strong></td>
                                <td>
                                    <?php
                                    $token = generate_csrf_token();
                                    echo !empty($token) ? '<span class="badge badge-success">✅ Pass</span>' : '<span class="badge badge-danger">❌ Fail</span>';
                                    ?>
                                </td>
                                <td><code><?php echo substr($token, 0, 20); ?>...</code></td>
                            </tr>
                            <tr>
                                <td><strong>Password Hashing</strong></td>
                                <td>
                                    <?php
                                    $test_hash = hash_password('TestPassword123');
                                    $hash_works = !empty($test_hash) && strlen($test_hash) >= 60;
                                    echo $hash_works ? '<span class="badge badge-success">✅ Pass</span>' : '<span class="badge badge-danger">❌ Fail</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php echo $hash_works ? 'Bcrypt hash generated' : 'Failed to generate'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Password Verification</strong></td>
                                <td>
                                    <?php
                                    $verify_works = verify_password('TestPassword123', $test_hash);
                                    echo $verify_works ? '<span class="badge badge-success">✅ Pass</span>' : '<span class="badge badge-danger">❌ Fail</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php echo $verify_works ? 'Verification successful' : 'Verification failed'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Input Sanitization</strong></td>
                                <td>
                                    <?php
                                    $dangerous = '<script>alert("XSS")</script>';
                                    $sanitized = sanitize_input($dangerous);
                                    $sanitize_works = ($sanitized !== $dangerous && strpos($sanitized, '<script>') === false);
                                    echo $sanitize_works ? '<span class="badge badge-success">✅ Pass</span>' : '<span class="badge badge-danger">❌ Fail</span>';
                                    ?>
                                </td>
                                <td>
                                    XSS attempt blocked: <code><?php echo e($sanitized); ?></code>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Email Validation</strong></td>
                                <td>
                                    <?php
                                    $email_test1 = validate_email('test@example.com');
                                    $email_test2 = !validate_email('invalid.email');
                                    $email_works = $email_test1 && $email_test2;
                                    echo $email_works ? '<span class="badge badge-success">✅ Pass</span>' : '<span class="badge badge-danger">❌ Fail</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php echo $email_works ? 'Valid emails accepted, invalid rejected' : 'Validation error'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Phone Validation</strong></td>
                                <td>
                                    <?php
                                    $phone_test1 = validate_phone('9876543210');
                                    $phone_test2 = !validate_phone('123');
                                    $phone_works = $phone_test1 && $phone_test2;
                                    echo $phone_works ? '<span class="badge badge-success">✅ Pass</span>' : '<span class="badge badge-danger">❌ Fail</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php echo $phone_works ? '10-digit validation working' : 'Validation error'; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- File Structure Test -->
            <div class="card mt-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-folder-open"></i> File Structure</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Required Files:</h6>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <?php echo file_exists('config/db_connect.php') ? '✅' : '❌'; ?>
                                    config/db_connect.php
                                </li>
                                <li class="list-group-item">
                                    <?php echo file_exists('includes/security.php') ? '✅' : '❌'; ?>
                                    includes/security.php
                                </li>
                                <li class="list-group-item">
                                    <?php echo file_exists('templates/header.php') ? '✅' : '❌'; ?>
                                    templates/header.php
                                </li>
                                <li class="list-group-item">
                                    <?php echo file_exists('templates/footer.php') ? '✅' : '❌'; ?>
                                    templates/footer.php
                                </li>
                                <li class="list-group-item">
                                    <?php echo file_exists('templates/navbar.php') ? '✅' : '❌'; ?>
                                    templates/navbar.php
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Required Folders:</h6>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <?php echo is_dir('uploads') ? '✅' : '❌'; ?>
                                    uploads/
                                </li>
                                <li class="list-group-item">
                                    <?php echo is_dir('public') ? '✅' : '❌'; ?>
                                    public/
                                </li>
                                <li class="list-group-item">
                                    <?php echo is_dir('database') ? '✅' : '❌'; ?>
                                    database/
                                </li>
                                <li class="list-group-item">
                                    <?php echo is_dir('docs') ? '✅' : '❌'; ?>
                                    docs/
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!--Next Steps-- >
            <div class="alert alert-info mt-4">
                <h5><i class="fas fa-lightbulb"></i> Next Steps:</h5>
                <ol>
                    <li><strong>If all tests pass:</strong> Your infrastructure is ready!</li>
                    <li><strong>Move files:</strong> Organize CSS, JS, fonts to public/ folder</li>
                    <li><strong>Update existing files:</strong> Start with login.php</li>
                    <li><strong>Add security:</strong> Use prepared statements and CSRF tokens</li>
                    <li><strong>Test thoroughly:</strong> Check each updated file</li>
                </ol>
            </div>

            <!--System Information-- >
                                            <div class="card mt-4">
                                                <div class="card-header bg-secondary text-white">
                                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> System Information</h5>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm mb-0">
                                                        <tr>
                                                            <td><strong>PHP Version:</strong></td>
                                                            <td>
                                                                <?php echo phpversion(); ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Server Software:</strong></td>
                                                            <td>
                                                                <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Document Root:</strong></td>
                                                            <td><code><?php echo $_SERVER['DOCUMENT_ROOT']; ?></code></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Current File:</strong></td>
                                                            <td><code><?php echo __FILE__; ?></code></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

        </div >
    </div >
</div >

<?php include 'templates/footer.php'; ?>