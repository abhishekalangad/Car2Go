<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_login();
require_role('driver');

$l_id = $_SESSION['l_id'];

// Fetch driver data
$query = "SELECT d.*, l.l_approve FROM driver_reg d JOIN login l ON d.dl_id = l.l_id WHERE d.dl_id = ?";
$driver = db_fetch_one($con, $query, "i", [$l_id]);

if (!$driver) {
    redirect_with_message('../index.php', 'Profile not found.', 'danger');
}

$page_title = 'Driver Dashboard - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<style>
    /* Premium Driver Theme */
    .driver-hero {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 4rem 0 6rem;
        color: white;
        margin-top: -20px;
        position: relative;
        border-radius: 0 0 40px 40px;
        overflow: hidden;
    }

    .driver-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('../images/bg3.jpg') center/cover;
        opacity: 0.15;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .driver-avatar-ring {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        padding: 5px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }

    .driver-avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
    }

    .stat-card-driver {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        transition: 0.3s;
    }

    .stat-card-driver:hover {
        transform: translateY(-5px);
        border-color: #3b82f6;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1.2rem;
    }

    .bg-soft-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .bg-soft-green {
        background: #f0fdf4;
        color: #22c55e;
    }

    .bg-soft-orange {
        background: #fff7ed;
        color: #f97316;
    }

    .job-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: 0.2s;
    }

    .job-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
    }

    .status-badge {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 5px 10px;
        border-radius: 20px;
    }

    .status-requested {
        background: #fefce8;
        color: #a16207;
    }

    .status-accepted {
        background: #dcfce7;
        color: #15803d;
    }

    .status-completed {
        background: #f1f5f9;
        color: #475569;
    }

    .quick-link-card {
        background: white;
        border-radius: 12px;
        padding: 1.2rem;
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        border: 1px solid #f1f5f9;
        color: #334155;
        text-decoration: none;
        transition: 0.2s;
    }

    .quick-link-card:hover {
        background: #f8fafc;
        transform: translateX(5px);
        color: #0f172a;
        text-decoration: none;
    }
</style>

<div class="driver-hero">
    <div class="container hero-content d-flex align-items-center">
        <div class="mr-4">
            <div class="driver-avatar-ring">
                <img src="../uploads/drivers/<?php echo e($driver['d_proof']); ?>" class="driver-avatar-img">
            </div>
        </div>
        <div>
            <div class="badge badge-success px-3 py-1 rounded-pill mb-2"><i class="fas fa-check-circle mr-1"></i>
                Verified Pro</div>
            <h1 class="font-weight-bold mb-0 text-white"><?php echo e($driver['d_name']); ?></h1>
            <p class="text-white-50 mt-1 mb-0"><i class="fas fa-map-marker-alt mr-2"></i>
                <?php echo e($driver['d_address']); ?></p>
        </div>
        <div class="ml-auto d-none d-md-block text-right">
            <div class="h4 font-weight-bold text-white mb-0">4.8 <small class="text-white-50">/ 5.0</small></div>
            <div class="small text-white-50">Overall Rating</div>
        </div>
    </div>
</div>

<div class="container pb-5" style="margin-top: -3.5rem; position: relative; z-index: 10;">
    <!-- Stats Row -->
    <div class="row mb-2">
        <?php
        // Fetch driver specific metrics
        $total_trips = db_fetch_one($con, "SELECT COUNT(*) as c FROM bookdriver WHERE dd_id = ? AND d_status = 'Completed'", "i", [$l_id])['c'] ?? 0;
        $pending_reqs = db_fetch_one($con, "SELECT COUNT(*) as c FROM bookdriver WHERE dd_id = ? AND d_status = 'Requested'", "i", [$l_id])['c'] ?? 0;
        ?>
        <div class="col-md-4">
            <div class="stat-card-driver">
                <div class="stat-icon bg-soft-orange"><i class="fas fa-bell"></i></div>
                <div>
                    <div class="text-muted small font-weight-bold">NEW REQUESTS</div>
                    <div class="h3 font-weight-bold mb-0 text-dark"><?php echo $pending_reqs; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-driver">
                <div class="stat-icon bg-soft-blue"><i class="fas fa-route"></i></div>
                <div>
                    <div class="text-muted small font-weight-bold">COMPLETED TRIPS</div>
                    <div class="h3 font-weight-bold mb-0 text-dark"><?php echo $total_trips; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-driver">
                <div class="stat-icon bg-soft-green"><i class="fas fa-rupee-sign"></i></div>
                <div>
                    <div class="text-muted small font-weight-bold">DAILY RATE</div>
                    <div class="h3 font-weight-bold mb-0 text-dark">₹<?php echo $driver['d_amount']; ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main: Job Feed -->
        <div class="col-lg-8">
            <h5 class="font-weight-bold text-dark mb-4">Current Assignments</h5>

            <?php
            // Using bookdriver table, dd_id is driver ID (from old schema logic)
            $gigs_query = "SELECT b.*, u.u_name, u.u_address, u.u_phone 
                          FROM bookdriver b 
                          JOIN login l ON b.dr_id = l.l_id 
                          JOIN user_reg u ON l.l_id = u.ul_id 
                          WHERE b.dd_id = ? 
                          ORDER BY b.bd_id DESC LIMIT 5";
            $gigs = db_fetch_all($con, $gigs_query, "i", [$l_id]);

            if (!empty($gigs)):
                foreach ($gigs as $g):
                    $status_class = match ($g['d_status']) {
                        'Requested' => 'status-requested',
                        'Approved' => 'status-accepted',
                        default => 'status-completed'
                    };
                    ?>
                    <div class="job-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-3 mr-3 text-secondary"><i class="fas fa-user"></i></div>
                                <div>
                                    <h6 class="font-weight-bold mb-0"><?php echo e($g['u_name']); ?></h6>
                                    <div class="small text-muted"><i class="fas fa-phone-alt font-xs mr-1"></i>
                                        <?php echo e($g['u_phone']); ?></div>
                                </div>
                            </div>
                            <span class="status-badge <?php echo $status_class; ?>"><?php echo e($g['d_status']); ?></span>
                        </div>

                        <div class="bg-light p-3 rounded mb-3">
                            <div class="row text-center">
                                <div class="col-6 border-right">
                                    <small class="text-muted d-block text-uppercase font-weight-bold"
                                        style="font-size:0.7rem;">Start Date</small>
                                    <div class="font-weight-bold"><?php echo date('d M, Y', strtotime($g['d_day1'])); ?></div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block text-uppercase font-weight-bold"
                                        style="font-size:0.7rem;">End Date</small>
                                    <div class="font-weight-bold"><?php echo date('d M, Y', strtotime($g['d_day2'])); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <?php if ($g['d_status'] == 'Requested'): ?>
                                <a href="assignments.php?action=accept&id=<?php echo $g['bd_id']; ?>"
                                    class="btn btn-primary btn-sm rounded-pill px-4 font-weight-bold shadow-sm">Accept Job</a>
                            <?php else: ?>
                                <a href="assignments.php?id=<?php echo $g['bd_id']; ?>"
                                    class="btn btn-outline-secondary btn-sm rounded-pill px-3">View Details</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                <div class="text-center py-5 bg-white rounded-lg border border-light shadow-sm">
                    <img src="../images/empty-state.svg" width="80" class="mb-3 opacity-5">
                    <h6 class="text-muted">No assignments found yet.</h6>
                    <p class="small text-muted">Keep your availability updated to get more jobs!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar: Tools -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <h5 class="font-weight-bold text-dark mb-4">Driver Tools</h5>

                <a href="../dedit.php" class="quick-link-card">
                    <div class="bg-primary text-white rounded p-2 mr-3"><i class="fas fa-user-edit"></i></div>
                    <div>
                        <div class="font-weight-bold">Edit Profile</div>
                        <div class="small text-muted">Update rates & info</div>
                    </div>
                </a>

                <a href="../driverprofile.php" class="quick-link-card">
                    <div class="bg-info text-white rounded p-2 mr-3"><i class="fas fa-id-card"></i></div>
                    <div>
                        <div class="font-weight-bold">My Documents</div>
                        <div class="small text-muted">Manage license</div>
                    </div>
                </a>

                <a href="../dvfeed.php" class="quick-link-card">
                    <div class="bg-warning text-white rounded p-2 mr-3"><i class="fas fa-star"></i></div>
                    <div>
                        <div class="font-weight-bold">My Ratings</div>
                        <div class="small text-muted">Customer feedback</div>
                    </div>
                </a>

                <div class="bg-dark rounded-lg p-4 text-white mt-4">
                    <h6 class="font-weight-bold text-warning mb-2"><i class="fas fa-lightbulb mr-2"></i> Pro Tip</h6>
                    <p class="small text-white-50 mb-0">Drivers with a complete profile and verified documents get 40%
                        more booking requests.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>