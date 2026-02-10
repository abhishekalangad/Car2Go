<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();

$rl_id = isset($_GET['dl_id']) ? (int) $_GET['dl_id'] : 0;
$l_id = $_SESSION['l_id'] ?? 0;

// Fetch driver details
$query = "SELECT d.*, l.l_approve FROM driver_reg d JOIN login l ON d.dl_id = l.l_id WHERE d.dl_id = ?";
$driver = db_fetch_one($con, $query, "i", [$rl_id]);

if (!$driver) {
    redirect_with_message('viewdriv.php', 'Driver profile not found.', 'danger');
}

// Fetch related drivers (similar location or random)
$related_query = "SELECT d.* FROM driver_reg d JOIN login l ON d.dl_id = l.l_id WHERE d.dl_id != ? AND l.l_approve = 'approve' ORDER BY RAND() LIMIT 3";
$related_drivers = db_fetch_all($con, $related_query, "i", [$rl_id]);

$error_message = '';
$success_message = '';

// Handle Booking Submission
if (isset($_POST['submit'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid request.';
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
    /* Premium Page Styles */
    .page-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 6rem 0 12rem;
        /* Increased bottom padding to prevent overlap */
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: -50px;
    }

    .page-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('uploads/drivers/<?php echo !empty($driver['d_proof']) ? $driver['d_proof'] : 'default.jpg'; ?>') center/cover;
        opacity: 0.15;
        filter: blur(8px);
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .driver-profile-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        position: relative;
        margin-top: -30px;
        /* Slight Negative margin to pull it up */
    }

    .profile-avatar {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        border: 6px solid white;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        object-fit: cover;
        margin-top: -110px;
        /* Determine overlap */
        background: #fff;
        position: relative;
        z-index: 5;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin: 2rem 0;
        text-align: center;
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 16px;
    }

    .stat-item h3 {
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.2rem;
    }

    .stat-item span {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
    }

    .booking-card {
        background: white;
        border: none;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        position: sticky;
        top: 100px;
        transition: transform 0.3s ease;
    }

    .booking-card:hover {
        transform: translateY(-5px);
    }

    .booking-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        padding: 3rem 2rem;
        color: white;
        text-align: center;
        position: relative;
    }

    .booking-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 70%);
        pointer-events: none;
    }

    .price-large {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1;
        text-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        margin: 0.5rem 0;
        display: block;
    }

    .booking-form-group {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 1rem;
        position: relative;
    }

    .booking-form-group:focus-within {
        background: white;
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        transform: translateY(-1px);
    }

    .booking-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
    }

    .booking-input {
        border: none;
        background: transparent;
        width: 100%;
        font-weight: 700;
        color: #1e293b;
        padding: 5px 0;
        outline: none;
        font-size: 1.1rem;
    }

    .btn-hire-visual {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border: none;
        padding: 1.25rem;
        font-size: 1.1rem;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        transition: all 0.3s;
    }

    .btn-hire-visual:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(37, 99, 235, 0.5);
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .form-control-lg {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }

    .verified-pill {
        background: #dcfce7;
        color: #166534;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
    }

    /* Detail Lists */
    .detail-list-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-list-item:last-child {
        border-bottom: none;
    }

    .detail-list-item i {
        width: 30px;
        color: #94a3b8;
    }

    .doc-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px;
        display: flex;
        align-items: center;
        transition: all 0.2s;
        text-decoration: none;
        color: #334155;
    }

    .doc-card:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        text-decoration: none;
        color: #3b82f6;
    }

    /* Helpers */
    .d-flex {
        display: flex;
    }

    .align-items-center {
        align-items: center;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    /* Mobile Optimization */
    @media (max-width: 991px) {
        .booking-card {
            position: static;
            margin-top: 2rem;
        }
        
        .page-hero {
            padding: 4rem 0 6rem;
        }
        
        .display-4 {
            font-size: 2.5rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            margin-top: -80px;
        }
    }

    @media (max-width: 768px) {
        .stat-grid {
            grid-template-columns: 1fr;
        }
        
        .price-large {
            font-size: 2.5rem;
        }
    }
</style>

<!-- Hero Section -->
<div class="page-hero">
    <div class="container hero-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-3">
                <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item"><a href="viewdriv.php" class="text-white-50">Drivers</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page"><?php echo e($driver['d_name']); ?>
                </li>
            </ol>
        </nav>
        <div class="text-center">
            <h1 class="display-4 font-weight-bold mb-2 text-white">Hire a Professional</h1>
            <p class="lead text-white-50">Trusted, verified, and experienced drivers at your service</p>
        </div>
    </div>
</div>

<div class="container pb-5" style="z-index: 10; position: relative;">

    <?php if ($success_message): ?>
        <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm">
            <i class="fas fa-check-circle fa-2x mr-3"></i>
            <div class="ml-3"><?php echo e($success_message); ?></div>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm">
            <i class="fas fa-exclamation-circle fa-2x mr-3"></i>
            <div class="ml-3"><?php echo e($error_message); ?></div>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Left Column: Profile Info -->
        <div class="col-lg-8">
            <div class="driver-profile-card">
                <div class="d-flex flex-column align-items-center text-center">
                    <img src="<?php echo !empty($driver['d_proof']) ? 'uploads/drivers/' . $driver['d_proof'] : 'images/default-avatar.png'; ?>"
                        class="profile-avatar mb-3" alt="Driver Profile" onerror="this.src='images/default-avatar.png'">
                    <h2 class="font-weight-bold mb-1"><?php echo e($driver['d_name']); ?></h2>
                    <div class="text-muted mb-2"><i class="fas fa-map-marker-alt mr-1"></i>
                        <?php echo e($driver['d_address']); ?></div>
                    <div class="verified-pill"><i class="fas fa-check-circle mr-1"></i> Verified Professional</div>
                </div>

                <div class="stat-grid">
                    <div class="stat-item">
                        <h3 class="text-warning">4.8</h3>
                        <span>Avg Rating</span>
                    </div>
                    <div class="stat-item">
                        <h3>5+</h3>
                        <span>Years Exp.</span>
                    </div>
                    <div class="stat-item">
                        <h3 class="text-success">100%</h3>
                        <span>Job Success</span>
                    </div>
                </div>

                <div class="mt-4">
                    <h5 class="font-weight-bold mb-3">Professional Details</h5>
                    <div class="detail-list-item">
                        <i class="fas fa-id-card"></i>
                        <span class="text-muted mr-2">Experience:</span>
                        <span class="font-weight-bold">Senior Chauffeur</span>
                    </div>
                    <div class="detail-list-item">
                        <i class="fas fa-language"></i>
                        <span class="text-muted mr-2">Languages:</span>
                        <span class="font-weight-bold">English, Hindi, Local</span>
                    </div>
                    <div class="detail-list-item">
                        <i class="fas fa-car"></i>
                        <span class="text-muted mr-2">Expertise:</span>
                        <span class="font-weight-bold">Automatic & Manual Transmission, Luxury Cars</span>
                    </div>
                    <div class="detail-list-item">
                        <i class="fas fa-clock"></i>
                        <span class="text-muted mr-2">Availability:</span>
                        <span class="font-weight-bold text-success">Available Now</span>
                    </div>
                </div>

                <h5 class="font-weight-bold mb-3 mt-5">Credentials</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="uploads/drivers/<?php echo e($driver['d_licence']); ?>" target="_blank"
                            class="doc-card">
                            <i class="fas fa-id-badge fa-2x mr-3 text-primary"></i>
                            <div>
                                <div class="font-weight-bold">Driving License</div>
                                <div class="small text-muted">Verified by Admin</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="doc-card bg-light" style="cursor: default;">
                            <i class="fas fa-user-shield fa-2x mr-3 text-success"></i>
                            <div>
                                <div class="font-weight-bold">Background Check</div>
                                <div class="small text-muted">Clean Record</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column: Booking Form -->
        <div class="col-lg-4">
            <div class="booking-card animate__animated animate__fadeInRight animate__delay-1s">
                <div class="booking-header">
                    <div class="small text-white-50 text-uppercase font-weight-bold letter-spacing-1 mb-2">Daily Rate
                    </div>
                    <span class="price-large">₹<?php echo number_format($driver['d_amount']); ?></span>
                    <div class="badge badge-light text-primary font-weight-bold px-3 py-1 rounded-pill mt-2 small">
                        <i class="fas fa-tag mr-1"></i> Best Price
                    </div>
                </div>

                <div class="booking-body p-4">
                    <form action="" method="post" id="bookingForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                        <div class="booking-form-group">
                            <label class="booking-label"><i class="far fa-calendar-alt mr-2 text-primary"></i> Start
                                Date</label>
                            <input type="date" id="day1" name="day1" class="booking-input" min="<?php echo date('Y-m-d'); ?>"
                                required>
                        </div>

                        <div class="booking-form-group mb-2">
                            <label class="booking-label"><i class="far fa-calendar-check mr-2 text-primary"></i> End
                                Date</label>
                            <input type="date" id="day2" name="day2" class="booking-input" min="<?php echo date('Y-m-d'); ?>"
                                required>
                        </div>

                        <!-- Price Summary -->
                        <div id="priceSummary" class="mb-4 d-none">
                            <div class="bg-light rounded-lg p-3">
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">Duration:</span>
                                    <span id="displayDays" class="font-weight-bold text-dark">0 days</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small border-bottom pb-2">
                                    <span class="text-muted">Daily Rate:</span>
                                    <span class="font-weight-bold text-dark">₹<?php echo number_format($driver['d_amount']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold text-dark">Estimated Total:</span>
                                    <span id="displayTotal" class="h5 font-weight-bold text-primary mb-0">₹0</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="submit"
                            class="btn btn-primary btn-block rounded-xl btn-hire-visual">
                            HIRE NOW <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-4 border-top">
                        <div class="d-flex align-items-center justify-content-center text-muted mb-2">
                            <i class="fas fa-shield-alt text-success fa-lg mr-2"></i>
                            <span class="font-weight-bold text-dark">100% Secure Usage</span>
                        </div>
                        <p class="text-muted extra-small mb-0">No prepayment required. Pay directly to driver.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Drivers -->
    <?php if (!empty($related_drivers)): ?>
        <div class="mt-5 pt-4">
            <h3 class="font-weight-bold mb-4">Similar Professionals</h3>
            <div class="row">
                <?php foreach ($related_drivers as $rd): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; overflow: hidden; transition: transform 0.3s; background: white;">
                            <div class="text-center pt-4">
                                <img src="<?php echo !empty($rd['d_proof']) ? 'uploads/drivers/' . $rd['d_proof'] : 'images/default-avatar.png'; ?>" 
                                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #f8fafc;" 
                                     onerror="this.src='images/default-avatar.png'">
                            </div>
                            <div class="card-body p-4 text-center">
                                <h5 class="font-weight-bold mb-1"><?php echo e($rd['d_name']); ?></h5>
                                <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt text-primary mr-1"></i> <?php echo e($rd['d_pincode']); ?></p>
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="badge badge-light px-3 py-2 rounded-pill font-weight-bold">₹<?php echo number_format($rd['d_amount']); ?>/day</div>
                                </div>
                                <a href="viewdriverprofile.php?dl_id=<?php echo $rd['dl_id']; ?>" class="btn btn-outline-primary btn-sm btn-block rounded-pill font-weight-bold">View Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .card:hover { transform: translateY(-5px); }
</style>

<?php include 'templates/footer.php'; ?>

<script>
   $(document).ready(function () {
      const day1Input = $('#day1');
      const day2Input = $('#day2');
      const priceSummary = $('#priceSummary');
      const displayDays = $('#displayDays');
      const displayTotal = $('#displayTotal');
      const dailyRate = <?php echo (int)$driver['d_amount']; ?>;

      function calculatePrice() {
         const date1 = new Date(day1Input.val());
         const date2 = new Date(day2Input.val());

         if (!isNaN(date1.getTime()) && !isNaN(date2.getTime())) {
            // Set date2 min to date1
            day2Input.attr('min', day1Input.val());

            const diffTime = date2.getTime() - date1.getTime();
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Inclusive of start day

            if (diffDays > 0) {
               const total = diffDays * dailyRate;
               displayDays.text(diffDays + (diffDays === 1 ? ' day' : ' days'));
               displayTotal.text('₹' + total.toLocaleString());
               priceSummary.removeClass('d-none').addClass('animate__animated animate__fadeIn');
            } else {
               priceSummary.addClass('d-none');
            }
         } else {
            priceSummary.addClass('d-none');
         }
      }

      day1Input.on('change', calculatePrice);
      day2Input.on('change', calculatePrice);
   });
</script>