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

<style>
    /* Admin Premium Styles */
    .admin-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 4rem 0 6rem;
        color: white;
        margin-top: -20px;
        position: relative;
        overflow: hidden;
        border-radius: 0 0 30px 30px;
    }

    .admin-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('../images/bg4.jpg') center/cover;
        opacity: 0.1;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .stat-card-premium {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        transition: transform 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .stat-card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 30px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .stat-icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.6;
    }

    .pending-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #fee2e2;
        color: #ef4444;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .bg-icon-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .bg-icon-green {
        background: #f0fdf4;
        color: #22c55e;
    }

    .bg-icon-purple {
        background: #f5f3ff;
        color: #8b5cf6;
    }

    .bg-icon-orange {
        background: #fff7ed;
        color: #ea580c;
    }

    .action-grid-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        transition: all 0.2s;
        text-decoration: none;
        color: #334155;
        margin-bottom: 1rem;
    }

    .action-grid-card:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateX(5px);
        color: #0f172a;
        text-decoration: none;
    }

    .table-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #f1f5f9;
        margin-top: 2rem;
    }

    .table-header {
        background: white;
        padding: 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-responsive th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        color: #64748b;
        font-weight: 700;
        border: none;
        padding: 1rem 1.5rem;
    }

    .table-responsive td {
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
        color: #334155;
    }
</style>

<div class="admin-hero">
    <div class="container hero-content">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-white text-dark rounded-circle p-3 mr-3 shadow-sm">
                <i class="fas fa-shield-alt fa-2x"></i>
            </div>
            <div>
                <h6 class="text-white-50 mb-0 text-uppercase">Admin Portal</h6>
                <h2 class="font-weight-bold mb-0">Dashboard Overview</h2>
            </div>
            <div class="ml-auto">
                <div class="text-right text-white-50 small">
                    <div><?php echo date('l, F j, Y'); ?></div>
                    <div class="text-white font-weight-bold">System Status: Nominal</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5" style="margin-top: -4rem; z-index: 10; position: relative;">
    <!-- Stats Grid -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <a href="users.php" class="text-decoration-none">
                <div class="stat-card-premium">
                    <div class="stat-icon-circle bg-icon-blue"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?php echo $stats['users']; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="drivers.php" class="text-decoration-none">
                <div class="stat-card-premium">
                    <?php if ($stats['pending_drivers'] > 0): ?>
                        <div class="pending-badge"><?php echo $stats['pending_drivers']; ?> New</div>
                    <?php endif; ?>
                    <div class="stat-icon-circle bg-icon-green"><i class="fas fa-user-tie"></i></div>
                    <div class="stat-number"><?php echo $stats['drivers']; ?></div>
                    <div class="stat-label">Verified Drivers</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="services.php" class="text-decoration-none">
                <div class="stat-card-premium">
                    <div class="stat-icon-circle bg-icon-purple"><i class="fas fa-tools"></i></div>
                    <div class="stat-number"><?php echo $stats['services']; ?></div>
                    <div class="stat-label">Service Centers</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="cars.php" class="text-decoration-none">
                <div class="stat-card-premium">
                    <?php if ($stats['pending_cars'] > 0): ?>
                        <div class="pending-badge"><?php echo $stats['pending_cars']; ?> New</div>
                    <?php endif; ?>
                    <div class="stat-icon-circle bg-icon-orange"><i class="fas fa-car"></i></div>
                    <div class="stat-number"><?php echo $stats['cars']; ?></div>
                    <div class="stat-label">Active Fleet</div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Main: Recent Activity -->
        <div class="col-lg-8">
            <div class="table-container">
                <div class="table-header">
                    <h5 class="font-weight-bold mb-0 text-dark">Recent Bookings</h5>
                    <a href="bookings.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Booking Info</th>
                                <th>Users Involved</th>
                                <th>Timeline</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_bookings)):
                                foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark">#<?php echo $booking['b_id']; ?></div>
                                            <div class="small text-muted">Car Rental</div>
                                        </td>
                                        <td>
                                            <div class="small"><span class="text-muted">By:</span>
                                                <?php echo e($booking['booker']); ?></div>
                                            <div class="small"><span class="text-muted">Owner:</span>
                                                <?php echo e($booking['owner']); ?></div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center small">
                                                <i class="fas fa-calendar-alt text-muted mr-2"></i>
                                                <div>
                                                    <div><?php echo $booking['b_day1']; ?></div>
                                                    <div class="text-muted">to <?php echo $booking['b_day2']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = 'secondary';
                                            if ($booking['b_status'] == 'Approved')
                                                $badge_class = 'success';
                                            if ($booking['b_status'] == 'Booked')
                                                $badge_class = 'warning';
                                            if ($booking['b_status'] == 'Cancelled')
                                                $badge_class = 'danger';
                                            ?>
                                            <span
                                                class="badge badge-<?php echo $badge_class; ?> px-2 py-1"><?php echo e($booking['b_status']); ?></span>
                                        </td>
                                        <td>
                                            <a href="bookings.php?id=<?php echo $booking['b_id']; ?>"
                                                class="btn btn-light btn-sm rounded-circle shadow-sm"><i
                                                    class="fas fa-chevron-right"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <p class="text-muted mb-0">No recent bookings found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar: Shortcuts -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-lg mb-4 mt-lg-0 mt-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="font-weight-bold m-0 text-dark">Quick Management</h6>
                </div>
                <div class="card-body p-3">
                    <a href="drivers.php?filter=pending" class="action-grid-card">
                        <div class="bg-danger text-white rounded p-2 mr-3"><i class="fas fa-user-check"></i></div>
                        <div>
                            <div class="font-weight-bold">Approve Drivers</div>
                            <div class="small text-muted text-danger"><?php echo $stats['pending_drivers']; ?> Pending
                                Requests</div>
                        </div>
                    </a>

                    <a href="cars.php?filter=pending" class="action-grid-card">
                        <div class="bg-warning text-white rounded p-2 mr-3"><i class="fas fa-car-side"></i></div>
                        <div>
                            <div class="font-weight-bold">Verify Vehicles</div>
                            <div class="small text-muted text-warning"><?php echo $stats['pending_cars']; ?> Pending
                                Listings</div>
                        </div>
                    </a>

                    <a href="services.php" class="action-grid-card">
                        <div class="bg-primary text-white rounded p-2 mr-3"><i class="fas fa-tools"></i></div>
                        <div>
                            <div class="font-weight-bold">Service Centers</div>
                            <div class="small text-muted">Manage Partners</div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="bg-dark rounded-lg p-4 text-white text-center">
                <i class="fas fa-server fa-3x mb-3 text-success"></i>
                <h5>System Healthy</h5>
                <p class="small text-white-50 mb-0">Database connections stable.<br>Security protocols active.</p>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>