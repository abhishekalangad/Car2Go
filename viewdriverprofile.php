<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

$rl_id = isset($_GET['dl_id']) ? (int)$_GET['dl_id'] : 0;
$l_id = $_SESSION['l_id'] ?? 0;

// Fetch driver details
$query = "SELECT d.*, l.l_approve FROM driver_reg d JOIN login l ON d.dl_id = l.l_id WHERE d.dl_id = ?";
$driver = db_fetch_one($con, $query, "i", [$rl_id]);

if (!$driver) {
    redirect_with_message('viewdriv.php', 'Driver profile not found.', 'danger');
}

$error_message = '';

// Handle Booking Submission
if (isset($_POST['submit'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid request.';
    } else if (!is_logged_in()) {
        redirect_with_message('login.php', 'Please login to book a driver.', 'info');
    } else {
        $day1 = $_POST['day1'];
        $day2 = $_POST['day2'];

        if (empty($day1) || empty($day2)) {
            $error_message = 'Please select both dates.';
        } else if (strtotime($day1) < strtotime(date('Y-m-d'))) {
            $error_message = 'Start date cannot be in the past.';
        } else if (strtotime($day2) < strtotime($day1)) {
            $error_message = 'End date cannot be before start date.';
        } else {
            $book_query = "INSERT INTO bookdriver (dr_id, dd_id, d_day1, d_day2, d_status) VALUES (?, ?, ?, ?, ?)";
            if (db_execute($con, $book_query, "iisss", [$l_id, $rl_id, $day1, $day2, 'Requested'])) {
                redirect_with_message('user/bookings.php', 'Driver booking request sent! Track its status here.', 'success');
            } else {
                $error_message = 'Failed to process request. Please try again.';
            }
        }
    }
}

$page_title = $driver['d_name'] . "'s Profile - CAR2GO";
include 'templates/header.php';
?>

<style>
    .driver-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 80px 0;
        margin-top: -20px;
        color: white;
    }
    .profile-img-lg {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        border: 8px solid rgba(255, 255, 255, 0.1);
        object-fit: cover;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
    }
    .booking-card {
        background: white;
        border-radius: 24px;
        margin-top: -80px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid #f1f5f9;
        position: relative;
    }
    .card-accent {
        height: 8px;
        background: linear-gradient(to right, var(--primary-color), var(--accent-color));
        width: 100%;
    }
    .stat-badge {
        display: inline-flex;
        align-items: center;
        background: #f8fafc;
        padding: 8px 16px;
        border-radius: 50px;
        margin-right: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #475569;
    }
    .badge-premium {
        background: var(--primary-color);
        color: white;
        border-radius: 50px;
        padding: 4px 12px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>

<div class="driver-header">
    <div class="container text-center">
        <img src="uploads/drivers/<?php echo e($driver['d_proof']); ?>" class="profile-img-lg mb-4" alt="Driver">
        <h1 class="display-4 font-weight-bold mb-2"><?php echo e($driver['d_name']); ?></h1>
        <div class="d-flex justify-content-center gap-3">
            <span class="stat-badge"><i class="fas fa-star text-warning mr-2"></i> 4.8 Rating</span>
            <span class="stat-badge"><i class="fas fa-certificate text-success mr-2"></i> Fully Verified</span>
        </div>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="booking-card">
                <div class="card-accent"></div>
                <div class="p-5">
                    <div class="row mb-5">
                        <div class="col-md-7">
                            <h3 class="font-weight-bold mb-3">About the Driver</h3>
                            <p class="text-muted leading-relaxed">Experienced professional driver with verified track record. Expert in handling premium sedans and SUVs, ensuring a smooth and safe transit experience. Familiar with all major city routes and inter-state highways.</p>
                            
                            <div class="row mt-4">
                                <div class="col-6 mb-3">
                                    <div class="small text-muted font-weight-bold mb-1">SERVICE AREA</div>
                                    <div class="font-weight-bold"><i class="fas fa-map-marker-alt text-primary mr-1"></i> <?php echo e($driver['d_address']); ?></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="small text-muted font-weight-bold mb-1">LANGUAGE</div>
                                    <div class="font-weight-bold"><i class="fas fa-globe text-primary mr-1"></i> English, Local</div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-muted font-weight-bold mb-1">MEMBER SINCE</div>
                                    <div class="font-weight-bold"><i class="fas fa-calendar-check text-primary mr-1"></i> Jan 2024</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="bg-light rounded-xl p-4 text-center">
                                <div class="h6 text-muted mb-1">Starting from</div>
                                <div class="h2 font-weight-bold text-primary mb-0">₹<?php echo e($driver['d_amount']); ?></div>
                                <div class="small text-muted uppercase">Per Professional Day</div>
                                <hr>
                                <div class="text-left small">
                                    <div class="mb-2"><i class="fas fa-check text-success mr-2"></i> Punctuality Guaranteed</div>
                                    <div class="mb-2"><i class="fas fa-check text-success mr-2"></i> No Smoking/Drinking</div>
                                    <div><i class="fas fa-check text-success mr-2"></i> Professional Attire</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="font-weight-bold mb-4">Credentials & Security</h4>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <a href="uploads/documents/<?php echo e($driver['d_licence']); ?>" target="_blank" class="card border p-3 text-dark text-decoration-none transition-hover shadow-sm">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3"><i class="fas fa-id-card fa-2x text-primary"></i></div>
                                    <div>
                                        <div class="font-weight-bold">Operating License</div>
                                        <div class="extra-small text-muted">Verified by CAR2GO Admin</div>
                                    </div>
                                    <div class="ml-auto"><i class="fas fa-external-link-alt text-muted"></i></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card border p-3 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3"><i class="fas fa-shield-check fa-2x text-success"></i></div>
                                    <div>
                                        <div class="font-weight-bold">Background Check</div>
                                        <div class="extra-small text-muted">Police Verification Cleared</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Booking Form -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <div class="card border-0 shadow-lg rounded-xl overflow-hidden mt-5 mt-lg-n5">
                    <div class="bg-primary p-4 text-center text-white">
                        <h5 class="font-weight-bold mb-0">Request a Service</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger small"><?php echo e($error_message); ?></div>
                        <?php endif; ?>

                        <form action="" method="post" class="premium-form">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            
                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-muted uppercase mb-2">Service From</label>
                                <input type="date" name="day1" class="form-control bg-light border-0 py-4" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-muted uppercase mb-2">Service To</label>
                                <input type="date" name="day2" class="form-control bg-light border-0 py-4" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <button type="submit" name="submit" class="btn btn-premium btn-gradient btn-block py-3 font-weight-bold shadow-sm">
                                SEND REQUEST
                            </button>
                        </form>
                        <div class="text-center mt-3 pt-3 border-top">
                            <p class="extra-small text-muted mb-0"><i class="fas fa-lock mr-1"></i> Payments are secured by CAR2GO Pay</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border p-4 mt-4">
                    <h6 class="font-weight-bold mb-2">Booking Policy</h6>
                    <ul class="extra-small text-muted mb-0 pl-3">
                        <li class="mb-2">Free cancellation before 24 hours.</li>
                        <li class="mb-2">Service charge includes driver fee only.</li>
                        <li>Fuel & Tolls must be paid separately.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-xl { border-radius: 20px; }
    .leading-relaxed { line-height: 1.8; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    .transition-hover:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; color: var(--primary-color) !important; }
</style>

<?php include 'templates/header.php'; ?>