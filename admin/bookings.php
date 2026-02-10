<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_role('admin');

// Fetch All Car Bookings
$car_bookings = db_fetch_all($con, "
    SELECT b.*, r.r_company, r.r_mname, u1.u_name as booker, u2.u_name as owner 
    FROM bookcar b
    JOIN rent r ON b.br_id = r.r_id
    JOIN user_reg u1 ON b.bo_id = u1.ul_id
    JOIN user_reg u2 ON r.rl_id = u2.ul_id
    ORDER BY b.b_id DESC
");

// Fetch All Driver Bookings
$driver_bookings = db_fetch_all($con, "
    SELECT b.*, u.u_name as customer, d.d_name as driver
    FROM bookdriver b
    JOIN user_reg u ON b.dr_id = u.ul_id
    JOIN driver_reg d ON b.dd_id = d.dl_id
    ORDER BY b.d_id DESC
");

$page_title = 'Global Booking Oversight - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="page-hero py-5"
    style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: white; margin-top:-20px; border-radius: 0 0 40px 40px;">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold mb-2">Transaction Logs</h1>
        <p class="opacity-7">Real-time overview of rental and service activity.</p>

        <div class="d-inline-flex bg-white bg-opacity-10 rounded-pill px-4 py-2 mt-4 backdrop-blur">
            <div class="mr-4">
                <span class="d-block small text-white-50 font-weight-bold uppercase">Total Volume</span>
                <span class="h5 font-weight-bold mb-0"><?php echo count($car_bookings) + count($driver_bookings); ?>
                    Orders</span>
            </div>
            <div class="border-left border-light opacity-5 mx-2"></div>
            <div class="ml-4">
                <span class="d-block small text-white-50 font-weight-bold uppercase">Active Cars</span>
                <span class="h5 font-weight-bold mb-0"><?php echo count($car_bookings); ?> Rentals</span>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5 py-5 mt-n5">
    <div class="row">
        <!-- Car Rentals Oversight -->
        <div class="col-xl-7 mb-4">
            <div class="card border-0 shadow-lg rounded-xl overflow-hidden h-100">
                <div class="card-header bg-white py-4 border-0">
                    <h5 class="font-weight-bold mb-0 text-dark"><i class="fas fa-car mr-2 text-primary"></i> Car Rentals
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 border-0 text-uppercase small font-weight-bold text-muted py-3">Vehicle
                                    Details</th>
                                <th class="border-0 text-uppercase small font-weight-bold text-muted py-3">Parties
                                    Involved</th>
                                <th class="border-0 text-uppercase small font-weight-bold text-center text-muted py-3">
                                    Status</th>
                                <th
                                    class="text-right px-4 border-0 text-uppercase small font-weight-bold text-muted py-3">
                                    Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($car_bookings as $b): ?>
                                <tr>
                                    <td class="px-4 align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 mr-3 text-primary">
                                                <i class="fas fa-key"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark small">
                                                    <?php echo e($b['r_company']); ?>     <?php echo e($b['r_mname']); ?>
                                                </div>
                                                <div class="extra-small text-muted">ID: #<?php echo $b['b_id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle small">
                                        <div><span class="text-muted">Booker:</span> <span
                                                class="font-weight-bold text-dark"><?php echo e($b['booker']); ?></span>
                                        </div>
                                        <div><span class="text-muted">Owner:</span> <?php echo e($b['owner']); ?></div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php
                                        $c_status = match ($b['b_status']) {
                                            'confirmed', 'Paid' => 'success',
                                            'Booked' => 'warning',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge badge-<?php echo $c_status; ?> pill px-3 py-1">
                                            <?php echo e($b['b_status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-right px-4 align-middle font-weight-bold text-dark">
                                        ₹<?php echo number_format($b['payment'] ?? 0); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Driver Gigs Oversight -->
        <div class="col-xl-5 mb-4">
            <div class="card border-0 shadow-lg rounded-xl overflow-hidden h-100">
                <div class="card-header bg-white py-4 border-0">
                    <h5 class="font-weight-bold mb-0 text-dark"><i class="fas fa-user-tie mr-2 text-primary"></i> Driver
                        Logs</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 border-0 text-uppercase small font-weight-bold text-muted py-3">Trip
                                    Info</th>
                                <th class="border-0 text-uppercase small font-weight-bold text-muted py-3">Driver</th>
                                <th
                                    class="text-right px-4 border-0 text-uppercase small font-weight-bold text-muted py-3">
                                    State</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($driver_bookings as $b): ?>
                                <tr>
                                    <td class="px-4 align-middle">
                                        <div class="font-weight-bold small text-dark">
                                            <?php echo e($b['customer']); ?>
                                        </div>
                                        <div class="extra-small text-muted">
                                            <?php echo date('M d', strtotime($b['d_day1'])); ?> -
                                            <?php echo date('M d', strtotime($b['d_day2'])); ?>
                                        </div>
                                    </td>
                                    <td class="align-middle small font-weight-bold text-primary">
                                        <?php echo e($b['driver']); ?>
                                    </td>
                                    <td class="align-middle text-right px-4">
                                        <?php
                                        $d_status = match ($b['d_status']) {
                                            'confirmed' => 'success',
                                            'Requested' => 'warning',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge badge-outline-<?php echo $d_status; ?> pill small px-2">
                                            <?php echo e($b['d_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-xl {
        border-radius: 1.5rem;
    }

    .extra-small {
        font-size: 0.7rem;
    }

    .uppercase {
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pill {
        border-radius: 30px;
    }

    .badge-outline-success {
        color: #10b981;
        border: 1px solid #10b981;
        background: transparent;
    }

    .badge-outline-warning {
        color: #f59e0b;
        border: 1px solid #f59e0b;
        background: transparent;
    }

    .badge-outline-secondary {
        color: #64748b;
        border: 1px solid #cbd5e1;
        background: transparent;
    }

    .bg-opacity-10 {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }

    .backdrop-blur {
        backdrop-filter: blur(5px);
    }
</style>

<?php include '../templates/header.php'; ?>