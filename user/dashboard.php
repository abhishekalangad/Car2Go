<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_login();
require_role('user');

$l_id = $_SESSION['l_id'];

// Fetch user data
$query = "SELECT * FROM user_reg WHERE ul_id = ?";
$user = db_fetch_one($con, $query, "i", [$l_id]);

if (!$user) {
    redirect_with_message('../index.php', 'Profile not found.', 'danger');
}

$page_title = 'User Dashboard - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<style>
    /* Premium Dashboard Styles */
    .dashboard-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 5rem 0 7rem;
        color: white;
        margin-top: -20px;
        border-radius: 0 0 50px 50px;
        margin-bottom: 2rem;
        position: relative;
    }

    .dashboard-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('../images/bg8.jpg') center/cover;
        opacity: 0.1;
        z-index: 0;
        border-radius: 0 0 50px 50px;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .user-avatar-circle {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: bold;
        color: white;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        border: 4px solid rgba(255, 255, 255, 0.2);
    }

    .action-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
        height: 100%;
    }

    .action-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border-color: #3b82f6;
    }

    .action-icon {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 1.5rem;
        color: #3b82f6;
        transition: 0.3s;
    }

    .action-card:hover .action-icon {
        background: #eff6ff;
        color: #2563eb;
    }

    .stats-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: #ecfdf5;
        color: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .table-custom th {
        border-top: none;
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 1rem;
    }

    .table-custom td {
        vertical-align: middle;
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-booked {
        background: #dcfce7;
        color: #166534;
    }

    .status-pending {
        background: #fef9c3;
        color: #854d0e;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }
</style>

<div class="dashboard-hero">
    <div class="container hero-content d-flex align-items-center">
        <div class="mr-4">
            <div class="user-avatar-circle">
                <?php echo strtoupper(substr($user['u_name'], 0, 1)); ?>
            </div>
        </div>
        <div>
            <h5 class="text-white-50 mb-1">Welcome back,</h5>
            <h1 class="font-weight-bold mb-2"><?php echo e($user['u_name']); ?></h1>
            <div class="d-flex align-items-center text-white-50 small">
                <span class="mr-3"><i class="fas fa-map-marker-alt mr-1"></i>
                    <?php echo e($user['u_address']); ?></span>
                <span><i class="fas fa-envelope mr-1"></i> <?php echo e($user['u_email']); ?></span>
            </div>
        </div>
        <div class="ml-auto d-none d-md-block">
            <a href="../logout.php" class="btn btn-outline-light rounded-pill px-4 font-weight-bold">
                <i class="fas fa-sign-out-alt mr-2"></i> Log Out
            </a>
        </div>
    </div>
</div>

<div class="container pb-5" style="margin-top: -6rem; position: relative; z-index: 10;">
    <!-- Quick Stats -->
    <div class="row mb-2">
        <?php
        // Fetch basic stats
        $active_bookings = db_fetch_one($con, "SELECT COUNT(*) as c FROM bookcar WHERE bo_id = ? AND b_status = 'Booked'", "i", [$l_id])['c'] ?? 0;
        $total_spent = 0; // Placeholder for now
        ?>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-car"></i></div>
                <div>
                    <div class="text-muted small font-weight-bold">ACTIVE RENTALS</div>
                    <div class="h4 font-weight-bold mb-0 text-dark"><?php echo $active_bookings; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon" style="background: #eff6ff; color: #3b82f6;"><i class="fas fa-wallet"></i></div>
                <div>
                    <div class="text-muted small font-weight-bold">TOTAL SPENT</div>
                    <div class="h4 font-weight-bold mb-0 text-dark">₹<?php echo number_format($total_spent); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon" style="background: #fff7ed; color: #ea580c;"><i class="fas fa-star"></i></div>
                <div>
                    <div class="text-muted small font-weight-bold">ACCOUNT STATUS</div>
                    <div class="h4 font-weight-bold mb-0 text-dark">Verified</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Actions -->
    <h5 class="font-weight-bold text-dark mb-4">Quick Actions</h5>
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="../viewcars.php" class="text-decoration-none">
                <div class="action-card">
                    <div class="action-icon"><i class="fas fa-search"></i></div>
                    <h5 class="font-weight-bold text-dark">Rent a Car</h5>
                    <p class="text-muted small mb-0">Browse latest models</p>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="../viewdriv.php" class="text-decoration-none">
                <div class="action-card">
                    <div class="action-icon"><i class="fas fa-user-tie"></i></div>
                    <h5 class="font-weight-bold text-dark">Hire Driver</h5>
                    <p class="text-muted small mb-0">Professional chauffeurs</p>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="../viewservicee1.php" class="text-decoration-none">
                <div class="action-card">
                    <div class="action-icon"><i class="fas fa-tools"></i></div>
                    <h5 class="font-weight-bold text-dark">Book Service</h5>
                    <p class="text-muted small mb-0">Maintenance & repair</p>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="../rentingform.php" class="text-decoration-none">
                <div class="action-card">
                    <div class="action-icon"><i class="fas fa-car-side"></i></div>
                    <h5 class="font-weight-bold text-dark">List Your Car</h5>
                    <p class="text-muted small mb-0">Earn with CAR2GO</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
        <div class="card-header bg-white py-4 px-4 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="font-weight-bold mb-0 text-dark">Recent Activity</h5>
                <a href="bookings.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 font-weight-bold">View
                    All</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Vehicle / Service</th>
                        <th>Dates</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $bookings_query = "SELECT b.*, r.r_company, r.r_mname FROM bookcar b JOIN rent r ON b.br_id = r.r_id WHERE b.bo_id = ? ORDER BY b.b_id DESC LIMIT 4";
                    $bookings = db_fetch_all($con, $bookings_query, "i", [$l_id]);

                    if (!empty($bookings)):
                        foreach ($bookings as $b):
                            $status_class = match ($b['b_status']) {
                                'Booked' => 'status-booked',
                                'Cancelled' => 'status-cancelled',
                                default => 'status-pending'
                            };
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 mr-3 text-primary"><i class="fas fa-car"></i></div>
                                        <div>
                                            <div class="font-weight-bold text-dark">
                                                <?php echo e($b['r_company'] . ' ' . $b['r_mname']); ?></div>
                                            <div class="small text-muted">Rental ID: #<?php echo $b['b_id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small font-weight-bold text-dark">
                                        <?php echo date('M d', strtotime($b['b_day1'])); ?> -
                                        <?php echo date('M d', strtotime($b['b_day2'])); ?></div>
                                    <div class="small text-muted text-xs"><?php echo date('Y', strtotime($b['b_day1'])); ?>
                                    </div>
                                </td>
                                <td><span class="badge badge-light text-muted">Self-Drive</span></td>
                                <td><span
                                        class="status-badge <?php echo $status_class; ?>"><?php echo e($b['b_status']); ?></span>
                                </td>
                                <td>
                                    <?php if ($b['b_status'] == 'Approved'): ?>
                                        <a href="../pay.php?id=<?php echo $b['b_id']; ?>"
                                            class="btn btn-primary btn-sm rounded-pill btn-sm px-3 shadow-sm">Pay Now</a>
                                    <?php else: ?>
                                        <a href="#" class="btn btn-light btn-sm rounded-pill px-3 text-muted disabled">Details</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <img src="../images/empty-state.svg" width="100" class="mb-3 opacity-5">
                                <p class="text-muted font-weight-bold">No recent bookings found.</p>
                                <a href="../viewcars.php" class="btn btn-primary btn-sm rounded-pill px-4">Start Booking</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>