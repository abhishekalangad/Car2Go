<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_login();
require_role('service center');

$l_id = $_SESSION['l_id'];

// Fetch service center data
$query = "SELECT s.*, l.l_approve FROM service_reg s JOIN login l ON s.sl_id = l.l_id WHERE s.sl_id = ?";
$service = db_fetch_one($con, $query, "i", [$l_id]);

if (!$service) {
    redirect_with_message('../index.php', 'Profile not found.', 'danger');
}

$page_title = 'Partner Dashboard - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<style>
    /* Premium Service Partner Theme */
    .partner-hero {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        padding: 4rem 0 6rem;
        color: white;
        margin-top: -20px;
        position: relative;
        border-radius: 0 0 40px 40px;
        overflow: hidden;
    }

    .partner-hero::before {
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

    .brand-logo-card {
        width: 120px;
        height: 120px;
        background: white;
        border-radius: 20px;
        padding: 10px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand-logo-img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 12px;
    }

    .dashboard-metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .metric-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        transition: 0.3s;
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        border-color: #6366f1;
    }

    .metric-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .bg-indigo-soft {
        background: #e0e7ff;
        color: #4338ca;
    }

    .bg-teal-soft {
        background: #ccfbf1;
        color: #0f766e;
    }

    .bg-rose-soft {
        background: #ffe4e6;
        color: #be123c;
    }

    .bg-amber-soft {
        background: #fef3c7;
        color: #b45309;
    }

    .task-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        transition: 0.2s;
    }

    .task-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
    }

    .status-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-progress {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .status-completed {
        background: #f0fdf4;
        color: #15803d;
    }

    .nav-tabs-custom {
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 1rem 1.5rem;
        position: relative;
    }

    .nav-tabs-custom .nav-link.active {
        color: #4338ca;
        background: transparent;
    }

    .nav-tabs-custom .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #4338ca;
    }

    @media (max-width: 991px) {
        .dashboard-metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="partner-hero">
    <div class="container hero-content d-flex align-items-center">
        <div class="mr-4">
            <div class="brand-logo-card">
                <img src="../uploads/services/<?php echo e($service['s_rc']); ?>" class="brand-logo-img" alt="Logo">
            </div>
        </div>
        <div>
            <div class="badge badge-light text-primary px-3 py-1 rounded-pill mb-2 font-weight-bold"><i
                    class="fas fa-certificate mr-1"></i> Authorized Partner</div>
            <h1 class="font-weight-bold mb-1 text-white"><?php echo e($service['s_name']); ?></h1>
            <p class="text-white-50 mt-1 mb-0"><i class="fas fa-map-marker-alt mr-2"></i>
                <?php echo e($service['s_address']); ?></p>
        </div>
        <div class="ml-auto d-none d-md-block text-right">
            <div class="h4 font-weight-bold text-white mb-0">98%</div>
            <div class="small text-white-50">Response Rate</div>
        </div>
    </div>
</div>

<div class="container pb-5" style="margin-top: -3.5rem; position: relative; z-index: 10;">

    <!-- Metrics Grid -->
    <div class="dashboard-metrics-grid">
        <?php
        // Fetch live counts
        $pending_tasks = db_fetch_one($con, "SELECT COUNT(*) as c FROM servicereq WHERE s_id = ? AND status='Pending'", "i", [$l_id])['c'];
        $active_jobs = db_fetch_one($con, "SELECT COUNT(*) as c FROM servicereq WHERE s_id = ? AND status='In Progress'", "i", [$l_id])['c'];
        $total_completed = db_fetch_one($con, "SELECT COUNT(*) as c FROM servicereq WHERE s_id = ? AND status='Completed'", "i", [$l_id])['c'];
        $avg_rating = number_format(db_fetch_one($con, "SELECT AVG(rating) as v FROM ratings WHERE l_id=?", "i", [$l_id])['v'] ?? 0, 1);
        ?>

        <a href="tasks.php" class="text-decoration-none">
            <div class="metric-card">
                <div class="d-flex justify-content-between">
                    <div class="metric-icon bg-indigo-soft"><i class="fas fa-clipboard-list"></i></div>
                    <?php if ($pending_tasks > 0): ?>
                        <span class="badge badge-danger rounded-pill align-self-start">Action Req.</span>
                    <?php endif; ?>
                </div>
                <div class="h3 font-weight-bold text-dark mb-0"><?php echo $pending_tasks; ?></div>
                <div class="text-muted small font-weight-bold text-uppercase">Pending Request</div>
            </div>
        </a>

        <div class="metric-card">
            <div class="metric-icon bg-teal-soft"><i class="fas fa-cogs"></i></div>
            <div class="h3 font-weight-bold text-dark mb-0"><?php echo $active_jobs; ?></div>
            <div class="text-muted small font-weight-bold text-uppercase">Jobs in Progress</div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-amber-soft"><i class="fas fa-trophy"></i></div>
            <div class="h3 font-weight-bold text-dark mb-0"><?php echo $total_completed; ?></div>
            <div class="text-muted small font-weight-bold text-uppercase">Total Completed</div>
        </div>

        <a href="../svfeed.php" class="text-decoration-none">
            <div class="metric-card">
                <div class="metric-icon bg-rose-soft"><i class="fas fa-star"></i></div>
                <div class="h3 font-weight-bold text-dark mb-0"><?php echo $avg_rating; ?></div>
                <div class="text-muted small font-weight-bold text-uppercase">Customer Rating</div>
            </div>
        </a>
    </div>

    <div class="row">
        <!-- Main: Task Feed -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-weight-bold text-dark mb-0">Service Queue</h5>
                <a href="tasks.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All Tasks</a>
            </div>

            <?php
            $reqs_query = "SELECT sr.*, u.u_name, u.u_phone FROM servicereq sr JOIN user_reg u ON sr.u_id = u.ul_id WHERE sr.s_id = ? ORDER BY sr.id DESC LIMIT 5";
            $reqs = db_fetch_all($con, $reqs_query, "i", [$l_id]);

            if (!empty($reqs)):
                foreach ($reqs as $r):
                    $status_class = match ($r['status']) {
                        'Pending' => 'status-pending',
                        'In Progress' => 'status-progress',
                        'Completed' => 'status-completed',
                        default => 'status-pending'
                    };
                    ?>
                    <div class="task-card">
                        <div class="mr-3">
                            <div class="bg-light rounded p-3 text-center" style="min-width: 70px;">
                                <div class="small font-weight-bold text-uppercase text-muted">
                                    <?php echo date('M', strtotime($r['date'])); ?></div>
                                <div class="h5 font-weight-bold mb-0 text-dark"><?php echo date('d', strtotime($r['date'])); ?>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="font-weight-bold text-dark mb-1"><?php echo e($r['v_name']); ?> -
                                    <?php echo e($r['s_name']); ?></h6>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo e($r['status']); ?></span>
                            </div>
                            <div class="text-muted small mb-1">Customer: <?php echo e($r['u_name']); ?> • <i
                                    class="fas fa-phone-alt font-xs ml-1"></i> <?php echo e($r['u_phone']); ?></div>
                            <?php if (!empty($r['rev'])): ?>
                                <div class="text-muted small font-italic mt-1"><i class="fas fa-quote-left mr-1 opacity-5"></i>
                                    <?php echo substr(e($r['rev']), 0, 50); ?>...</div>
                            <?php endif; ?>
                        </div>
                        <div class="ml-3 border-left pl-3">
                            <a href="tasks.php?id=<?php echo $r['id']; ?>" class="btn btn-light btn-sm rounded-circle"><i
                                    class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                <div class="text-center py-5 bg-white rounded-lg border border-light shadow-sm">
                    <img src="../images/empty-state.svg" width="80" class="mb-3 opacity-5">
                    <h6 class="text-muted">No pending tasks.</h6>
                    <p class="small text-muted">You're all caught up!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <div class="bg-white border rounded-lg p-4 mb-4 shadow-sm">
                    <h6 class="font-weight-bold text-dark mb-3">Partner Status</h6>
                    <div class="d-flex align-items-center mb-3">
                        <div class="progress flex-grow-1 mr-3" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                        <span class="small font-weight-bold text-success">Verified</span>
                    </div>
                    <p class="small text-muted mb-0">Your profile is visible to all users. Keep maintaining high ratings
                        to appear in top searches.</p>
                </div>

                <div class="list-group shadow-sm rounded-lg overflow-hidden">
                    <a href="../sedit.php"
                        class="list-group-item list-group-item-action border-0 py-3 font-weight-bold text-dark">
                        <i class="fas fa-cog text-muted mr-3"></i> Center Settings
                    </a>
                    <a href="../serviceform.php"
                        class="list-group-item list-group-item-action border-0 py-3 font-weight-bold text-dark">
                        <i class="fas fa-list-ul text-muted mr-3"></i> Manage Services
                    </a>
                    <a href="../svfeed.php"
                        class="list-group-item list-group-item-action border-0 py-3 font-weight-bold text-dark">
                        <i class="fas fa-comments text-muted mr-3"></i> Read Reviews
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>