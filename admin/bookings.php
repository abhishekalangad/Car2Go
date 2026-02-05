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

<div class="container-fluid py-5 px-5">
    <div class="mb-5 d-flex justify-content-between align-items-end">
        <div>
            <h1 class="font-weight-bold mb-1 border-left border-primary pl-3 border-4">Booking Analytics</h1>
            <p class="text-muted mb-0">Monitor all transactions and logistics across the platform.</p>
        </div>
        <div class="d-flex gap-3">
            <div class="bg-white p-3 rounded-lg shadow-sm border text-center" style="min-width: 150px;">
                <div class="small text-muted font-weight-bold uppercase mb-1">Total Bookings</div>
                <div class="h4 font-weight-bold mb-0 text-primary">
                    <?php echo count($car_bookings) + count($driver_bookings); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Car Rentals Oversight -->
        <div class="col-lg-7 mb-5">
            <div class="card border-0 shadow-sm rounded-xl">
                <div class="card-header bg-white py-4 border-0">
                    <h5 class="font-weight-bold mb-0"><i class="fas fa-car mr-2 text-primary"></i> Car Rental
                        Transactions</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light small font-weight-bold uppercase">
                            <tr>
                                <th class="px-4">Vehicle</th>
                                <th>Booker</th>
                                <th>Owner</th>
                                <th>Status</th>
                                <th class="text-right px-4">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($car_bookings as $b): ?>
                                <tr>
                                    <td class="px-4 align-middle">
                                        <div class="font-weight-bold small">
                                            <?php echo e($b['r_company']); ?>
                                            <?php echo e($b['r_mname']); ?>
                                        </div>
                                        <div class="extra-small text-muted">ID: #
                                            <?php echo $b['b_id']; ?>
                                        </div>
                                    </td>
                                    <td class="align-middle small">
                                        <?php echo e($b['booker']); ?>
                                    </td>
                                    <td class="align-middle small">
                                        <?php echo e($b['owner']); ?>
                                    </td>
                                    <td class="align-middle">
                                        <span
                                            class="badge badge-<?php echo ($b['b_status'] === 'confirmed' || $b['b_status'] === 'Paid') ? 'success' : 'warning'; ?> pill">
                                            <?php echo e($b['b_status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-right px-4 align-middle font-weight-bold">
                                        ₹
                                        <?php echo number_format($b['payment'] ?? 0, 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Driver Gigs Oversight -->
        <div class="col-lg-5 mb-5">
            <div class="card border-0 shadow-sm rounded-xl">
                <div class="card-header bg-white py-4 border-0">
                    <h5 class="font-weight-bold mb-0"><i class="fas fa-user-tie mr-2 text-primary"></i> Driver
                        Assignment Logs</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light small font-weight-bold uppercase">
                            <tr>
                                <th class="px-4">Log</th>
                                <th>Driver</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($driver_bookings as $b): ?>
                                <tr>
                                    <td class="px-4 align-middle">
                                        <div class="font-weight-bold small">
                                            <?php echo e($b['customer']); ?> hired driver
                                        </div>
                                        <div class="extra-small text-muted">
                                            <?php echo $b['d_day1']; ?> to
                                            <?php echo $b['d_day2']; ?>
                                        </div>
                                    </td>
                                    <td class="align-middle small">
                                        <?php echo e($b['driver']); ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-outline-secondary pill small px-2">
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
        font-size: 0.65rem;
    }

    .uppercase {
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pill {
        border-radius: 30px;
        font-weight: 600;
        padding: 4px 12px;
    }

    .border-4 {
        border-left-width: 6px !important;
    }

    .badge-outline-secondary {
        border: 1px solid #cbd5e1;
        color: #64748b;
        background: transparent;
    }
</style>

<?php include '../templates/header.php'; ?>