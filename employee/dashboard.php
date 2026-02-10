<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_login();
require_role('employe');

$l_id = $_SESSION['l_id'];

// Fetch employee data
$query = "SELECT * FROM emp_reg WHERE el_id = ?";
$emp = db_fetch_one($con, $query, "i", [$l_id]);

if (!$emp) {
    redirect_with_message('../index.php', 'Profile not found.', 'danger');
}

// Fetch Stats
$total_users = db_fetch_one($con, "SELECT COUNT(*) as c FROM user_reg")['c'] ?? 0;
$total_drivers = db_fetch_one($con, "SELECT COUNT(*) as c FROM driver_reg")['c'] ?? 0;
$total_services = db_fetch_one($con, "SELECT COUNT(*) as c FROM service_reg")['c'] ?? 0;
$pending_approvals = db_fetch_one($con, "SELECT COUNT(*) as c FROM login WHERE l_approve = 'pending'")['c'] ?? 0;

$page_title = 'Team Dashboard - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<style>
    /* Premium Employee Dashboard */
    .emp-hero {
        background: linear-gradient(135deg, #134e4a 0%, #0d9488 100%);
        padding: 4rem 0 6rem;
        color: white;
        margin-top: -20px;
        position: relative;
        border-radius: 0 0 40px 40px;
        overflow: hidden;
    }

    .emp-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('../images/bg8.jpg') center/cover;
        opacity: 0.1;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .avatar-square {
        width: 100px;
        height: 100px;
        background: white;
        color: #0d9488;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 800;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .metric-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        transition: 0.3s;
        text-align: center;
        height: 100%;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        border-color: #0d9488;
        box-shadow: 0 20px 40px rgba(13, 148, 136, 0.1);
    }

    .metric-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #f0fdfa;
        color: #0d9488;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
    }

    .action-row {
        margin-top: -3rem;
        position: relative;
        z-index: 10;
    }

    .quick-link {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        border: 1px solid #f1f5f9;
        color: #334155;
        text-decoration: none;
        transition: 0.2s;
    }

    .quick-link:hover {
        background: #f8fafc;
        transform: translateX(5px);
        color: #0f172a;
        text-decoration: none;
        border-color: #0d9488;
    }

    .quick-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1.5rem;
    }
</style>

<div class="emp-hero">
    <div class="container hero-content d-flex align-items-center">
        <div class="mr-4">
            <div class="avatar-square">
                <?php echo strtoupper(substr($emp['e_name'], 0, 1)); ?>
            </div>
        </div>
        <div>
            <div class="badge badge-light text-teal px-3 py-1 rounded-pill mb-2 font-weight-bold" style="color:#0d9488">
                <i class="fas fa-shield-alt mr-1"></i> Corporate Team Member
            </div>
            <h1 class="font-weight-bold mb-1 text-white"><?php echo e($emp['e_name']); ?></h1>
            <p class="text-white-50 mt-1 mb-0"><i class="fas fa-id-badge mr-2"></i> Employee ID:
                #<?php echo $emp['e_id']; ?>
            </p>
        </div>
        <div class="ml-auto d-none d-md-block text-right">
            <a href="../logout.php" class="btn btn-outline-light rounded-pill px-4 font-weight-bold">
                <i class="fas fa-sign-out-alt mr-2"></i> Log Out
            </a>
        </div>
    </div>
</div>

<div class="container pb-5 action-row">
    <!-- Metrics Grid -->
    <div class="row mb-5">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-users"></i></div>
                <div class="h3 font-weight-bold text-dark mb-0"><?php echo $total_users; ?></div>
                <div class="text-muted small font-weight-bold text-uppercase">Total Users</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fas fa-user-tie"></i>
                </div>
                <div class="h3 font-weight-bold text-dark mb-0"><?php echo $total_drivers; ?></div>
                <div class="text-muted small font-weight-bold text-uppercase">Total Drivers</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-store"></i></div>
                <div class="h3 font-weight-bold text-dark mb-0"><?php echo $total_services; ?></div>
                <div class="text-muted small font-weight-bold text-uppercase">Service Partners</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-icon" style="background:#fff7ed; color:#ea580c;"><i class="fas fa-clock"></i></div>
                <div class="h3 font-weight-bold text-dark mb-0"><?php echo $pending_approvals; ?></div>
                <div class="text-muted small font-weight-bold text-uppercase">Pending Approvals</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Management Links -->
        <div class="col-lg-7">
            <h5 class="font-weight-bold text-dark mb-4">Internal Management</h5>

            <a href="../viewuser.php" class="quick-link">
                <div class="quick-icon bg-info text-white"><i class="fas fa-users-cog"></i></div>
                <div>
                    <div class="font-weight-bold">Customer Directory</div>
                    <div class="small text-muted">Manage user status & accounts</div>
                </div>
                <i class="fas fa-chevron-right ml-auto text-muted"></i>
            </a>

            <a href="../viewdriv.php" class="quick-link">
                <div class="quick-icon bg-primary text-white"><i class="fas fa-clipboard-check"></i></div>
                <div>
                    <div class="font-weight-bold">Driver Verifications</div>
                    <div class="small text-muted">Review professional driver applications</div>
                </div>
                <i class="fas fa-chevron-right ml-auto text-muted"></i>
            </a>

            <a href="../viewservicee1.php" class="quick-link">
                <div class="quick-icon bg-success text-white"><i class="fas fa-building"></i></div>
                <div>
                    <div class="font-weight-bold">Service Partners</div>
                    <div class="small text-muted">Oversee authorized repair centers</div>
                </div>
                <i class="fas fa-chevron-right ml-auto text-muted"></i>
            </a>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-5">
            <div class="bg-white rounded-lg shadow-sm p-4 border h-100">
                <h5 class="font-weight-bold text-dark mb-3">Today's Focus</h5>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-3">
                        <i class="fas fa-check-circle text-success mt-1 mr-3"></i>
                        <div>
                            <div class="font-weight-bold small text-dark">Document Review</div>
                            <p class="small text-muted mb-0">Verify 5 new driver licenses submitted today.</p>
                        </div>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="fas fa-check-circle text-success mt-1 mr-3"></i>
                        <div>
                            <div class="font-weight-bold small text-dark">Partner Support</div>
                            <p class="small text-muted mb-0">Help 2 service centers update their pricing list.</p>
                        </div>
                    </li>
                    <li class="d-flex">
                        <i class="fas fa-check-circle text-muted mt-1 mr-3"></i>
                        <div>
                            <div class="font-weight-bold small text-muted">Weekly Report</div>
                            <p class="small text-muted mb-0">Prepare team performance summary for Friday.</p>
                        </div>
                    </li>
                </ul>

                <hr class="my-4">

                <div class="bg-light rounded p-3">
                    <h6 class="font-weight-bold small text-uppercase text-muted mb-2">Team Alert</h6>
                    <p class="small text-dark mb-0">New security protocol for user verification goes live tomorrow.
                        Please review the handbook.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>