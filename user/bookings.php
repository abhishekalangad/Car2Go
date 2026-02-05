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

<div class="container py-5">
    <div class="mb-5 text-center">
        <h1 class="display-4 font-weight-bold">My <span class="text-primary">Bookings</span></h1>
        <p class="text-muted">Track your active rentals and service requests in one place.</p>
    </div>

    <!-- Booking Navigation Tabs -->
    <ul class="nav nav-pills nav-justified mb-5 shadow-sm rounded-pill p-1 bg-white" id="bookingTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active rounded-pill font-weight-bold" id="cars-tab" data-toggle="tab" href="#cars"
                role="tab">
                <i class="fas fa-car mr-2"></i> Car Rentals
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill font-weight-bold" id="drivers-tab" data-toggle="tab" href="#drivers"
                role="tab">
                <i class="fas fa-user-tie mr-2"></i> Driver Hires
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill font-weight-bold" id="services-tab" data-toggle="tab" href="#services"
                role="tab">
                <i class="fas fa-tools mr-2"></i> Services
            </a>
        </li>
    </ul>

    <div class="tab-content" id="bookingTabsContent">
        <!-- Car Rentals Tab -->
        <div class="tab-pane fade show active" id="cars" role="tabpanel">
            <div class="row">
                <?php foreach ($car_bookings as $booking): ?>
                    <?php
                    $days = max(1, ceil((strtotime($booking['b_day2']) - strtotime($booking['b_day1'])) / (60 * 60 * 24)));
                    $total = $days * $booking['rent_amt'];
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm rounded-xl overflow-hidden h-100">
                            <div class="row no-gutters h-100">
                                <div class="col-4">
                                    <img src="../uploads/cars/<?php echo e($booking['r_car']); ?>" class="img-fluid h-100"
                                        style="object-fit: cover;" alt="Car">
                                </div>
                                <div class="col-8 p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="font-weight-bold mb-0">
                                                <?php echo e($booking['r_company']); ?>
                                                <?php echo e($booking['r_mname']); ?>
                                            </h5>
                                            <span
                                                class="badge badge-<?php echo ($booking['b_status'] === 'confirmed' || $booking['b_status'] === 'Paid') ? 'success' : 'warning'; ?> pill px-3">
                                                <?php echo e($booking['b_status']); ?>
                                            </span>
                                        </div>
                                        <div class="small text-muted mb-3">
                                            <div class="mb-1"><i class="fas fa-calendar-alt mr-2"></i>
                                                <?php echo $booking['b_day1']; ?> to
                                                <?php echo $booking['b_day2']; ?>
                                            </div>
                                            <div><i class="fas fa-user mr-2"></i> Owner:
                                                <?php echo e($booking['owner_name']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end mt-3">
                                        <div class="h5 font-weight-bold text-primary mb-0">₹
                                            <?php echo number_format($total, 2); ?>
                                        </div>
                                        <div>
                                            <?php if ($booking['b_status'] === 'confirmed' && empty($booking['payment'])): ?>
                                                <a href="../pay.php?id=<?php echo $booking['b_id']; ?>"
                                                    class="btn btn-sm btn-primary rounded-pill px-4">Pay Now</a>
                                            <?php elseif ($booking['b_status'] === 'Booked'): ?>
                                                <a href="bookings.php?action=cancel&type=car&id=<?php echo $booking['b_id']; ?>"
                                                    class="btn btn-sm btn-outline-danger border-0"
                                                    onclick="return confirm('Cancel this booking?')">Cancel</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($car_bookings)): ?>
                    <div class="col-12 text-center py-5 text-muted">No car rentals found. <a href="../viewcars.php">Book one
                            now!</a></div>
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
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm rounded-xl p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle p-2 mr-3"
                                        style="width:45px; height:45px; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-weight-bold mb-0">
                                            <?php echo e($booking['d_name']); ?>
                                        </h5>
                                        <div class="small text-muted"><i class="fas fa-phone mr-1"></i>
                                            <?php echo e($booking['d_phone']); ?>
                                        </div>
                                    </div>
                                </div>
                                <span
                                    class="badge badge-<?php echo ($booking['d_status'] === 'confirmed') ? 'success' : 'info'; ?> pill px-3">
                                    <?php echo e($booking['d_status']); ?>
                                </span>
                            </div>
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="small text-muted font-weight-bold uppercase extra-small mb-1">Duration</div>
                                    <div class="small">
                                        <?php echo $booking['d_day1']; ?> -
                                        <?php echo $booking['d_day2']; ?>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                    <div class="small text-muted font-weight-bold uppercase extra-small mb-1">Total Quote
                                    </div>
                                    <div class="font-weight-bold text-dark">₹
                                        <?php echo number_format($total, 2); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <?php if ($booking['d_status'] === 'Requested'): ?>
                                    <a href="bookings.php?action=cancel&type=driver&id=<?php echo $booking['d_id']; ?>"
                                        class="btn btn-sm btn-outline-danger border-0"
                                        onclick="return confirm('Cancel this request?')">Cancel Request</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($driver_bookings)): ?>
                    <div class="col-12 text-center py-5 text-muted">No driver bookings found. <a href="../viewdriv.php">Hire
                            a professional!</a></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Services Tab -->
        <div class="tab-pane fade" id="services" role="tabpanel">
            <div class="row">
                <?php
                $service_query = "SELECT sr.*, s.s_name as center_name 
                                  FROM servicereq sr 
                                  JOIN service_reg s ON sr.s_id = s.sl_id 
                                  WHERE sr.u_id = ? 
                                  ORDER BY sr.id DESC";
                $service_bookings = db_fetch_all($con, $service_query, "i", [$l_id]);
                foreach ($service_bookings as $booking):
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm rounded-xl p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="font-weight-bold mb-1"><i class="fas fa-tools mr-2 text-primary"></i>
                                    <?php echo e($booking['s_name']); ?>
                                </h5>
                                <span class="badge badge-primary pill px-3">
                                    <?php echo e($booking['status']); ?>
                                </span>
                            </div>
                            <div class="small text-muted mb-3">
                                <div><i class="fas fa-car mr-2"></i>
                                    <?php echo e($booking['v_name']); ?>
                                </div>
                                <div><i class="fas fa-building mr-2"></i> Center:
                                    <?php echo e($booking['center_name']); ?>
                                </div>
                                <div><i class="fas fa-calendar-alt mr-2"></i> Requested Date:
                                    <?php echo e($booking['date']); ?>
                                </div>
                            </div>
                            <div class="bg-light rounded p-2 small">
                                <strong>My Note:</strong>
                                <?php echo e($booking['rev']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($service_bookings)): ?>
                    <div class="col-12 text-center py-5 text-muted">No service history. <a href="../viewservicee1.php">Find
                            a service center!</a></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-xl {
        border-radius: 1.5rem;
    }

    .extra-small {
        font-size: 0.65rem;
    }

    .pill {
        border-radius: 30px;
    }

    .nav-pills .nav-link.active {
        background: var(--primary-color) !important;
        color: white !important;
    }

    .nav-pills .nav-link {
        color: #64748b;
    }

    .nav-pills .nav-link:hover {
        color: var(--primary-color);
    }
</style>

<?php include '../templates/header.php'; ?>