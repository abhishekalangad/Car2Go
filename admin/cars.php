<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

require_role('admin');

// Handle Status Updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];
    $new_status = ($action === 'approve') ? 'approve' : 'not approve';

    $query = "UPDATE rent SET r_status = ? WHERE r_id = ?";
    if (db_execute($con, $query, "si", [$new_status, $id])) {
        redirect_with_message('cars.php', 'Car listing status updated.', 'success');
        exit();
    }
}

// Fetch Cars
$query = "SELECT r.*, u.u_name as owner_name FROM rent r JOIN user_reg u ON r.rl_id = u.ul_id";
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($filter === 'pending') {
    $query .= " WHERE r.r_status = 'not approve'";
} else if ($filter === 'approved') {
    $query .= " WHERE r.r_status = 'approve'";
}
$cars = db_fetch_all($con, $query);

$page_title = 'Manage Car Listings - CAR2GO';
$base_url = '../';
include '../templates/header.php';
?>

<div class="page-hero py-5"
    style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: white; margin-top:-20px; border-radius: 0 0 40px 40px;">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold mb-2">Car Inventory</h1>
        <p class="opacity-7">Approve listings and verify vehicle documentation.</p>

        <div class="btn-group mt-4 shadow-sm rounded-pill overflow-hidden">
            <a href="cars.php?filter=all"
                class="btn btn-light <?php echo $filter === 'all' ? 'active font-weight-bold text-primary' : 'text-muted'; ?> px-4">All
                Cars</a>
            <a href="cars.php?filter=approved"
                class="btn btn-light <?php echo $filter === 'approved' ? 'active font-weight-bold text-primary' : 'text-muted'; ?> px-4">Approved</a>
            <a href="cars.php?filter=pending"
                class="btn btn-light <?php echo $filter === 'pending' ? 'active font-weight-bold text-primary' : 'text-muted'; ?> px-4">Pending</a>
        </div>
    </div>
</div>

<div class="container py-5 mt-n5">
    <div class="row">
        <?php foreach ($cars as $car): ?>
            <div class="col-xl-6 mb-4">
                <div class="card shadow-lg h-100 border-0 overflow-hidden car-card rounded-xl">
                    <div class="row no-gutters h-100">
                        <div class="col-md-5 position-relative">
                            <img src="../uploads/cars/<?php echo e($car['r_car']); ?>" class="card-img h-100 w-100"
                                style="object-fit: cover;" onerror="this.src='../images/bg3.jpg'">
                            <div class="position-absolute top-0 left-0 p-2">
                                <span class="badge badge-light shadow-sm"><?php echo e($car['r_year']); ?></span>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="card-body d-flex flex-column h-100">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title font-weight-bold mb-0 text-dark">
                                        <?php echo e($car['r_company']); ?>     <?php echo e($car['r_mname']); ?>
                                    </h5>
                                </div>

                                <div class="mb-3">
                                    <?php if ($car['r_status'] === 'approve'): ?>
                                        <span class="badge badge-success pill px-2 py-1 small"><i
                                                class="fas fa-check-circle mr-1"></i> Public</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning pill px-2 py-1 small"><i class="fas fa-clock mr-1"></i>
                                            Await Approval</span>
                                    <?php endif; ?>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted text-uppercase font-weight-bold"
                                            style="font-size:0.65rem;">Owner</small>
                                        <div class="font-weight-bold text-dark small"><?php echo e($car['owner_name']); ?>
                                        </div>
                                    </div>
                                    <div class="col-6 text-right">
                                        <small class="text-muted text-uppercase font-weight-bold"
                                            style="font-size:0.65rem;">Daily Rent</small>
                                        <div class="font-weight-bold text-primary h6 mb-0">
                                            ₹<?php echo number_format($car['rent_amt']); ?></div>
                                    </div>
                                </div>

                                <div class="bg-light rounded p-2 mb-3 d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i>
                                        <?php echo e($car['r_pincode']); ?></small>
                                    <small class="text-muted font-monospace"><?php echo e($car['r_number']); ?></small>
                                </div>

                                <div class="mt-auto">
                                    <div class="mb-2 d-flex justify-content-between">
                                        <small class="text-muted font-weight-bold">DOCS:</small>
                                        <div>
                                            <a href="../uploads/documents/<?php echo e($car['r_tax']); ?>" target="_blank"
                                                class="text-secondary mr-2" title="RC Book"><i
                                                    class="fas fa-file-contract"></i></a>
                                            <a href="../uploads/documents/<?php echo e($car['r_insurance']); ?>"
                                                target="_blank" class="text-secondary mr-2" title="Insurance"><i
                                                    class="fas fa-shield-alt"></i></a>
                                            <a href="../uploads/documents/<?php echo e($car['r_polution']); ?>"
                                                target="_blank" class="text-secondary" title="Pollution"><i
                                                    class="fas fa-smog"></i></a>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <?php if ($car['r_status'] !== 'approve'): ?>
                                            <a href="cars.php?action=approve&id=<?php echo $car['r_id']; ?>"
                                                class="btn btn-sm btn-primary flex-grow-1 rounded-pill shadow-sm font-weight-bold">Approve</a>
                                        <?php else: ?>
                                            <a href="cars.php?action=disapprove&id=<?php echo $car['r_id']; ?>"
                                                class="btn btn-sm btn-outline-danger flex-grow-1 rounded-pill font-weight-bold">Hide</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($cars)): ?>
            <div class="col-12 text-center py-5">
                <img src="../images/empty-state.svg" width="100" class="mb-3 opacity-5">
                <h5 class="text-muted">No cars found.</h5>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .car-card {
        transition: transform 0.2s;
    }

    .car-card:hover {
        transform: translateY(-5px);
    }

    .rounded-xl {
        border-radius: 1.25rem;
    }

    .pill {
        border-radius: 50px;
    }

    .gap-2 {
        gap: 0.5rem;
    }
</style>

<?php include '../templates/footer.php'; ?>