<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_login();
require_role('user');

$l_id = $_SESSION['l_id'];

// Handle Action (Cancel Booking etc)
if (isset($_GET['action']) && isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = (int) $_GET['id'];

    if ($type === 'car') {
        $q = "DELETE FROM bookcar WHERE b_id = ? AND bo_id = ? AND b_status = 'Booked'";
        if (db_execute($con, $q, "ii", [$id, $l_id])) {
            redirect_with_message('bookings.php', 'Car booking cancelled.', 'success');
        }
    } else if ($type === 'driver') {
        $q = "DELETE FROM bookdriver WHERE d_id = ? AND dr_id = ? AND d_status = 'Requested'";
        if (db_execute($con, $q, "ii", [$id, $l_id])) {
            redirect_with_message('bookings.php', 'Driver booking request cancelled.', 'success');
        }
    }
}

// Fetch Car Bookings
$car_query = "SELECT b.*, r.r_company, r.r_mname, r.r_car, r.rent_amt, u.u_name as owner_name 
              FROM bookcar b 
              JOIN rent r ON b.br_id = r.r_id 
              JOIN user_reg u ON r.rl_id = u.ul_id
              WHERE b.bo_id = ? 
              ORDER BY b.b_id DESC";
$car_bookings = db_fetch_all($con, $car_query, "i", [$l_id]);

// Fetch Driver Bookings
$driver_query = "SELECT b.*, d.d_name, d.d_phone, d.d_amount
                 FROM bookdriver b
                 JOIN driver_reg d ON b.dd_id = d.dl_id
                 WHERE b.dr_id = ?
                 ORDER BY b.d_id DESC";
$driver_bookings = db_fetch_all($con, $driver_query, "i", [$l_id]);

$page_title = 'My Bookings - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="booking-hero py-5"
    style="background: var(--bg-dark); color: white; margin-top:-20px; border-radius: 0 0 40px 40px;">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold mb-2">My Activity</h1>
        <p class="opacity-7">Manage your active rentals, driver hires, and service requests.</p>
    </div>
</div>

<div class="container py-5 mt-n5">

    <!-- Booking Navigation Tabs -->
    <div class="d-flex justify-content-center mb-5 position-relative z-index-10">
        <ul class="nav nav-pills shadow-lg rounded-pill p-1 bg-white" id="bookingTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active rounded-pill font-weight-bold px-4" id="cars-tab" data-toggle="tab"
                    href="#cars" role="tab">
                    <i class="fas fa-car mr-2"></i> Rentals
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill font-weight-bold px-4" id="drivers-tab" data-toggle="tab"
                    href="#drivers" role="tab">
                    <i class="fas fa-user-tie mr-2"></i> Drivers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill font-weight-bold px-4" id="services-tab" data-toggle="tab"
                    href="#services" role="tab">
                    <i class="fas fa-tools mr-2"></i> Services
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="bookingTabsContent">
        <!-- Car Rentals Tab -->
        <div class="tab-pane fade show active" id="cars" role="tabpanel">
            <div class="row">
                <?php foreach ($car_bookings as $booking): ?>
                    <?php
                    $days = max(1, ceil((strtotime($booking['b_day2']) - strtotime($booking['b_day1'])) / (60 * 60 * 24)));
                    $total = $days * $booking['rent_amt'];
                    $status_color = match ($booking['b_status']) {
                        'confirmed', 'Paid' => 'success',
                        'Booked' => 'warning',
                        default => 'secondary'
                    };
                    $status_text = match ($booking['b_status']) {
                        'Booked' => 'Pending Approval',
                        'confirmed' => 'Approved',
                        default => $booking['b_status']
                    };
                    ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card border-0 shadow-sm rounded-xl overflow-hidden h-100 booking-card">
                            <div class="row no-gutters h-100">
                                <div class="col-5 position-relative">
                                    <img src="../uploads/cars/<?php echo e($booking['r_car']); ?>"
                                        class="img-fluid h-100 w-100" style="object-fit: cover;" alt="Car">
                                    <div class="position-absolute bottom-0 left-0 w-100 p-2 text-white text-center"
                                        style="background: rgba(0,0,0,0.6); backdrop-filter: blur(2px);">
                                        <small class="font-weight-bold text-uppercase"><?php echo $days; ?> Day
                                            Rental</small>
                                    </div>
                                </div>
                                <div class="col-7 p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="font-weight-bold mb-0 text-dark">
                                                <?php echo e($booking['r_company']); ?>
                                                <?php echo e($booking['r_mname']); ?>
                                            </h5>
                                        </div>
                                        <div class="mb-3">
                                            <span class="badge badge-<?php echo $status_color; ?> pill px-2 py-1 small">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </div>
                                        <div class="small text-muted mb-3">
                                            <div class="mb-1"><i class="fas fa-calendar-alt text-primary mr-2"></i>
                                                <?php echo date('M d', strtotime($booking['b_day1'])); ?> -
                                                <?php echo date('M d', strtotime($booking['b_day2'])); ?></div>
                                            <div><i class="fas fa-user-circle text-primary mr-2"></i> Owner:
                                                <?php echo e($booking['owner_name']); ?></div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end mt-2">
                                        <div class="h5 font-weight-bold text-dark mb-0">
                                            ₹<?php echo number_format($total); ?></div>
                                        <div>
                                            <?php if ($booking['b_status'] === 'confirmed' && empty($booking['payment'])): ?>
                                                <a href="../pay.php?id=<?php echo $booking['b_id']; ?>"
                                                    class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm font-weight-bold">Pay
                                                    Now</a>
                                            <?php elseif ($booking['b_status'] === 'Booked'): ?>
                                                <a href="bookings.php?action=cancel&type=car&id=<?php echo $booking['b_id']; ?>"
                                                    class="btn btn-sm btn-outline-danger border-0 rounded-pill px-2"
                                                    onclick="return confirm('Cancel this booking?')">Cancel</a>
                                            <?php elseif ($booking['b_status'] === 'Paid'): ?>
                                                <span class="text-success small font-weight-bold"><i
                                                        class="fas fa-check-circle"></i> Paid</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($car_bookings)): ?>
                    <div class="col-12 text-center py-5">
                        <img src="../images/empty-state.svg" width="120" class="mb-3 opacity-5">
                        <h5 class="text-muted">No active rentals.</h5>
                        <a href="../viewcars.php" class="btn btn-link">Find a car to rent &rarr;</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Driver Hires Tab -->
        <div class="tab-pane fade" id="drivers" role="tabpanel">
            <div class="row">
                <?php foreach ($driver_bookings as $booking): ?>
                    <?php
                    $days = max(1, ceil((strtotime($booking['d_day2']) - strtotime($booking['d_day1'])) / (60 * 60 * 24)));
                    $total = $days * $booking['d_amount'];
                    ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card border-0 shadow-sm rounded-xl p-4 booking-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light text-primary rounded-circle p-3 mr-3 shadow-sm">
                                        <i class="fas fa-user-tie fa-lg"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-weight-bold mb-0 text-dark"><?php echo e($booking['d_name']); ?>
                                        </h5>
                                        <div class="small text-muted">Pro Driver</div>
                                    </div>
                                </div>
                                <span
                                    class="badge badge-<?php echo ($booking['d_status'] === 'confirmed') ? 'success' : 'warning'; ?> pill px-3 py-1">
                                    <?php echo e($booking['d_status']); ?>
                                </span>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted text-uppercase font-weight-bold"
                                        style="font-size:0.7rem;">Dates</small>
                                    <div class="font-weight-bold text-dark small">
                                        <?php echo date('M d', strtotime($booking['d_day1'])); ?> -
                                        <?php echo date('M d', strtotime($booking['d_day2'])); ?></div>
                                </div>
                                <div class="col-6 text-right">
                                    <small class="text-muted text-uppercase font-weight-bold"
                                        style="font-size:0.7rem;">Total Quote</small>
                                    <div class="font-weight-bold text-dark h6 mb-0">₹<?php echo number_format($total); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2">
                                <a href="tel:<?php echo e($booking['d_phone']); ?>"
                                    class="btn btn-light btn-sm rounded-pill text-muted"><i
                                        class="fas fa-phone-alt mr-1"></i> Call</a>
                                <?php if ($booking['d_status'] === 'Requested'): ?>
                                    <a href="bookings.php?action=cancel&type=driver&id=<?php echo $booking['d_id']; ?>"
                                        class="text-danger small font-weight-bold text-decoration-none"
                                        onclick="return confirm('Cancel this request?')">Cancel Request</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($driver_bookings)): ?>
                    <div class="col-12 text-center py-5">
                        <img src="../images/empty-state.svg" width="120" class="mb-3 opacity-5">
                        <h5 class="text-muted">No driver bookings.</h5>
                        <a href="../viewdriv.php" class="btn btn-link">Hire a professional driver &rarr;</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Services Tab -->
        <div class="tab-pane fade" id="services" role="tabpanel">
            <div class="row">
                <?php
                $service_query = "SELECT sr.*, s.s_name as center_name, sr.status 
                                  FROM servicereq sr 
                                  JOIN service_reg s ON sr.s_id = s.sl_id 
                                  WHERE sr.u_id = ? 
                                  ORDER BY sr.id DESC";
                $service_bookings = db_fetch_all($con, $service_query, "i", [$l_id]);
                foreach ($service_bookings as $booking):
                    $s_color = match ($booking['status']) {
                        'Approved', 'Completed' => 'success',
                        'Pending' => 'warning',
                        default => 'secondary'
                    };
                    ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card border-0 shadow-sm rounded-xl p-4 booking-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="font-weight-bold mb-1 text-dark"><?php echo e($booking['center_name']); ?>
                                    </h5>
                                    <div class="small text-muted"><i class="fas fa-car mr-1"></i>
                                        <?php echo e($booking['v_name']); ?></div>
                                </div>
                                <span
                                    class="badge badge-<?php echo $s_color; ?> pill px-3"><?php echo e($booking['status']); ?></span>
                            </div>

                            <div class="bg-light rounded p-3 mb-3">
                                <div class="small text-muted text-uppercase font-weight-bold mb-1"
                                    style="font-size:0.65rem;">Service Request</div>
                                <div class="text-dark small font-italic">"<?php echo e($booking['rev']); ?>"</div>
                            </div>

                            <div class="text-right">
                                <small
                                    class="text-muted font-weight-bold mr-2"><?php echo date('M d, Y', strtotime($booking['date'])); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($service_bookings)): ?>
                    <div class="col-12 text-center py-5">
                        <img src="../images/empty-state.svg" width="120" class="mb-3 opacity-5">
                        <h5 class="text-muted">No service history.</h5>
                        <a href="../viewservicee1.php" class="btn btn-link">Book a service &rarr;</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .booking-card {
        transition: transform 0.2s;
    }

    .booking-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
    }

    .rounded-xl {
        border-radius: 1.25rem;
    }

    .pill {
        border-radius: 50px;
    }

    .nav-pills .nav-link.active {
        background: var(--primary-color) !important;
        color: white !important;
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
    }

    .nav-pills .nav-link {
        color: #64748b;
        transition: all 0.3s;
    }

    .nav-pills .nav-link:hover {
        background: #f1f5f9;
        color: var(--primary-color);
    }

    .z-index-10 {
        position: relative;
        z-index: 10;
    }
</style>

<?php include '../templates/header.php'; ?>