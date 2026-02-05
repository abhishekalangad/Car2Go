<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

// Ensure only admin can access
require_role('admin');

// Fetch Stats
$stats = [
    'users' => db_fetch_one($con, "SELECT COUNT(*) as count FROM login WHERE l_type = 'user'")['count'],
    'drivers' => db_fetch_one($con, "SELECT COUNT(*) as count FROM login WHERE l_type = 'driver'")['count'],
    'services' => db_fetch_one($con, "SELECT COUNT(*) as count FROM login WHERE l_type = 'service center'")['count'],
    'cars' => db_fetch_one($con, "SELECT COUNT(*) as count FROM rent")['count'],
    'pending_drivers' => db_fetch_one($con, "SELECT COUNT(*) as count FROM login WHERE l_type = 'driver' AND l_approve = 'not approve'")['count'],
    'pending_cars' => db_fetch_one($con, "SELECT COUNT(*) as count FROM rent WHERE r_status = 'not approve'")['count']
];

// Fetch Recent Bookings
$recent_bookings = db_fetch_all($con, "
    SELECT b.*, u1.u_name as booker, u2.u_name as owner 
    FROM bookcar b
    JOIN user_reg u1 ON b.bo_id = u1.ul_id
    JOIN rent r ON b.br_id = r.r_id
    JOIN user_reg u2 ON r.rl_id = u2.ul_id
    ORDER BY b.b_id DESC LIMIT 5
");

$page_title = 'Admin Dashboard - CAR2GO';
// We need to adjust paths for header since we're in a sub-folder
$base_url = '../';
include '../templates/header.php';
?>

<div class="container-fluid py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="font-weight-bold text-dark"><i class="fas fa-tachometer-alt mr-2 text-primary"></i> Admin Control
                Panel</h2>
            <p class="text-muted">Master overview of CAR2GO operations.</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card shadow-sm border-0 rounded-lg p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                        <div class="h3 mb-0 font-weight-bold">
                            <?php echo $stats['users']; ?>
                        </div>
                    </div>
                    <div class="icon-circle bg-primary-light text-primary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
                <a href="users.php" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card shadow-sm border-0 rounded-lg p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Drivers</div>
                        <div class="h3 mb-0 font-weight-bold">
                            <?php echo $stats['drivers']; ?>
                        </div>
                        <small class="text-danger font-weight-bold">
                            <?php echo $stats['pending_drivers']; ?> Pending
                        </small>
                    </div>
                    <div class="icon-circle bg-success-light text-success">
                        <i class="fas fa-user-tie fa-2x"></i>
                    </div>
                </div>
                <a href="drivers.php" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card shadow-sm border-0 rounded-lg p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Partner Services</div>
                        <div class="h3 mb-0 font-weight-bold">
                            <?php echo $stats['services']; ?>
                        </div>
                    </div>
                    <div class="icon-circle bg-info-light text-info">
                        <i class="fas fa-tools fa-2x"></i>
                    </div>
                </div>
                <a href="services.php" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card shadow-sm border-0 rounded-lg p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Listed Cars</div>
                        <div class="h3 mb-0 font-weight-bold">
                            <?php echo $stats['cars']; ?>
                        </div>
                        <small class="text-danger font-weight-bold">
                            <?php echo $stats['pending_cars']; ?> Pending
                        </small>
                    </div>
                    <div class="icon-circle bg-warning-light text-warning">
                        <i class="fas fa-car fa-2x"></i>
                    </div>
                </div>
                <a href="cars.php" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Bookings Table -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Booking Requests</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Booker</th>
                                    <th>Car Owner</th>
                                    <th>Dates</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td>#
                                            <?php echo $booking['b_id']; ?>
                                        </td>
                                        <td>
                                            <?php echo e($booking['booker']); ?>
                                        </td>
                                        <td>
                                            <?php echo e($booking['owner']); ?>
                                        </td>
                                        <td>
                                            <small class="d-block text-muted">From:
                                                <?php echo $booking['b_day1']; ?>
                                            </small>
                                            <small class="d-block text-muted">To:
                                                <?php echo $booking['b_day2']; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-<?php echo ($booking['b_status'] == 'Booked' ? 'warning' : 'success'); ?> p-2">
                                                <?php echo e($booking['b_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="bookings.php?id=<?php echo $booking['b_id']; ?>"
                                                class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Alerts / Shortcuts -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="drivers.php?filter=pending" class="btn btn-danger btn-block text-left mb-2">
                        <i class="fas fa-user-check mr-2"></i> Approve New Drivers
                    </a>
                    <a href="cars.php?filter=pending" class="btn btn-warning btn-block text-left mb-2">
                        <i class="fas fa-car-side mr-2"></i> Verify New Car Listings
                    </a>
                    <a href="services.php?filter=pending" class="btn btn-info btn-block text-left mb-2">
                        <i class="fas fa-tools mr-2"></i> Manage Service Centers
                    </a>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-lg bg-primary text-white p-4 text-center">
                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                <h5>Secured Environment</h5>
                <p class="small opacity-8">All admin actions are monitored and protected by CSRF and Role-Based Access
                    Control.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .icon-circle {
        height: 60px;
        width: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-primary-light {
        background: #eff6ff;
    }

    .bg-success-light {
        background: #f0fdf4;
    }

    .bg-info-light {
        background: #ecfeff;
    }

    .bg-warning-light {
        background: #fffbeb;
    }
</style>

<?php include '../templates/footer.php'; ?>