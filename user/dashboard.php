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
    .profile-hero {
        background: linear-gradient(135deg, var(--bg-dark) 0%, #1e293b 100%);
        padding: 60px 0;
        margin-top: -20px;
        color: white;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .dashboard-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="profile-hero">
    <div class="container d-flex align-items-center">
        <div class="profile-avatar mr-4">
            <?php echo strtoupper(substr($user['u_name'], 0, 1)); ?>
        </div>
        <div>
            <h1 class="font-weight-bold mb-1">Welcome,
                <?php echo e($user['u_name']); ?>!
            </h1>
            <p class="text-white-50 mb-0"><i class="fas fa-map-marker-alt mr-2"></i>
                <?php echo e($user['u_address']); ?>
            </p>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="dashboard-card p-4 text-center">
                <div class="text-primary mb-3"><i class="fas fa-search fa-3x"></i></div>
                <h5 class="font-weight-bold">Find a Car</h5>
                <p class="small text-muted">Browse our fleet and book your next ride.</p>
                <a href="../viewcars.php" class="btn btn-primary btn-sm rounded-pill px-4">Browse Fleet</a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="dashboard-card p-4 text-center">
                <div class="text-success mb-3"><i class="fas fa-calendar-check fa-3x"></i></div>
                <h5 class="font-weight-bold">My Bookings</h5>
                <p class="small text-muted">Track your active and past rental history.</p>
                <a href="bookings.php" class="btn btn-success btn-sm rounded-pill px-4">View My History</a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="dashboard-card p-4 text-center">
                <div class="text-info mb-3"><i class="fas fa-plus-circle fa-3x"></i></div>
                <h5 class="font-weight-bold">List My Car</h5>
                <p class="small text-muted">Register your own vehicle for renting.</p>
                <a href="../rentingform.php" class="btn btn-info btn-sm rounded-pill px-4">Add Vehicle</a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Placeholder -->
    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="m-0 font-weight-bold text-dark">Recent Car Rentals</h6>
        </div>
        <div class="card-body p-0">
            <?php
            $bookings_query = "SELECT b.*, r.r_company, r.r_mname FROM bookcar b JOIN rent r ON b.br_id = r.r_id WHERE b.bo_id = ? ORDER BY b.b_id DESC LIMIT 3";
            $bookings = db_fetch_all($con, $bookings_query, "i", [$l_id]);
            ?>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Vehicle</th>
                            <th>Pickup</th>
                            <th>Return</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><strong>
                                        <?php echo e($b['r_company']); ?>
                                        <?php echo e($b['r_mname']); ?>
                                    </strong></td>
                                <td>
                                    <?php echo $b['b_day1']; ?>
                                </td>
                                <td>
                                    <?php echo $b['b_day2']; ?>
                                </td>
                                <td><span class="badge badge-warning">
                                        <?php echo e($b['b_status']); ?>
                                    </span></td>
                                <td><a href="../pay.php?id=<?php echo $b['b_id']; ?>"
                                        class="btn btn-sm btn-outline-primary">Pay Now</a></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No recent bookings found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>