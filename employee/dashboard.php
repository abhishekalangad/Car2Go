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

$page_title = 'Employee Dashboard - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="profile-hero"
    style="background: linear-gradient(135deg, #134e4a 0%, #0d9488 100%); padding: 60px 0; color: white;">
    <div class="container d-flex align-items-center">
        <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center"
            style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: 700;">
            <?php echo strtoupper(substr($emp['e_name'], 0, 1)); ?>
        </div>
        <div class="ml-4">
            <h1 class="font-weight-bold mb-1">
                <?php echo e($emp['e_name']); ?>
            </h1>
            <div class="badge badge-light px-3 py-1 rounded-pill">Corporate Team</div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100 transition-hover border-left-teal">
                <div class="d-flex align-items-center">
                    <div class="text-teal mr-4"><i class="fas fa-users-cog fa-3x"></i></div>
                    <div>
                        <h5 class="font-weight-bold mb-1">Manage Users</h5>
                        <p class="text-muted small mb-0">Help customers with their accounts.</p>
                    </div>
                </div>
                <a href="../viewuser.php" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100 transition-hover border-left-teal">
                <div class="d-flex align-items-center">
                    <div class="text-teal mr-4"><i class="fas fa-headset fa-3x"></i></div>
                    <div>
                        <h5 class="font-weight-bold mb-1">Support Desk</h5>
                        <p class="text-muted small mb-0">Respond to customer inquiries.</p>
                    </div>
                </div>
                <a href="../contact.php" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-lg mt-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-dark">My Team Stats</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-6 mb-3">
                    <div class="h3 font-weight-bold text-teal">0</div>
                    <small class="text-muted uppercase">Solved Tickets</small>
                </div>
                <div class="col-6 mb-3">
                    <div class="h3 font-weight-bold text-teal">--</div>
                    <small class="text-muted uppercase">Active Shifts</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-teal {
        color: #0d9488;
    }

    .border-left-teal {
        border-left: 5px solid #0d9488 !important;
    }

    .transition-hover:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
    }
</style>

<?php include '../templates/footer.php'; ?>