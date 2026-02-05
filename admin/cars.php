<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/security.php';

// Ensure only admin can access
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
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$query = "SELECT r.*, u.u_name as owner_name FROM rent r JOIN user_reg u ON r.rl_id = u.ul_id";
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

<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold">Manage Car Listings</h2>
            <p class="text-muted">Verify car details and documentation for rental approval.</p>
        </div>
        <div class="btn-group shadow-sm">
            <a href="cars.php?filter=all" class="btn btn-white <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
            <a href="cars.php?filter=approved"
                class="btn btn-white <?php echo $filter === 'approved' ? 'active' : ''; ?>">Approved</a>
            <a href="cars.php?filter=pending"
                class="btn btn-white <?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending <span
                    class="badge badge-danger ml-1">!</span></a>
        </div>
    </div>

    <div class="row">
        <?php foreach ($cars as $car): ?>
            <div class="col-xl-6 mb-4">
                <div class="card shadow-sm h-100 border-0 overflow-hidden">
                    <div class="row no-gutters">
                        <div class="col-md-4">
                            <img src="../images/<?php echo e($car['r_car'] ?: 'default-car.jpg'); ?>" class="card-img h-100"
                                style="object-fit: cover;" alt="Car Photo">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title font-weight-bold mb-0">
                                        <?php echo e($car['r_company']); ?>
                                        <?php echo e($car['r_mname']); ?>
                                    </h5>
                                    <?php if ($car['r_status'] === 'approve'): ?>
                                        <span class="badge badge-success px-2 py-1">Approved</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning px-2 py-1">Pending Approval</span>
                                    <?php endif; ?>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block uppercase">Owner</small>
                                        <span class="font-weight-bold">
                                            <?php echo e($car['owner_name']); ?>
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block uppercase">Daily Rent</small>
                                        <span class="font-weight-bold">₹
                                            <?php echo e($car['rent_amt']); ?>
                                        </span>
                                    </div>
                                </div>

                                <p class="card-text small text-muted mb-3">
                                    <i class="fas fa-id-card mr-1"></i> Plate:
                                    <?php echo e($car['r_number']); ?> |
                                    <i class="fas fa-map-marker-alt mr-1"></i> Pincode:
                                    <?php echo e($car['r_pincode']); ?>
                                </p>

                                <div class="d-flex align-items-center mb-4">
                                    <div class="mr-3">
                                        <small class="text-muted d-block">Documents</small>
                                        <div class="btn-group btn-group-sm">
                                            <a href="../uploads/documents/<?php echo e($car['r_tax']); ?>" target="_blank"
                                                class="btn btn-outline-secondary" title="RC/Tax"><i
                                                    class="fas fa-file-contract"></i></a>
                                            <a href="../uploads/documents/<?php echo e($car['r_insurance']); ?>"
                                                target="_blank" class="btn btn-outline-secondary" title="Insurance"><i
                                                    class="fas fa-shield-alt"></i></a>
                                            <a href="../uploads/documents/<?php echo e($car['r_polution']); ?>"
                                                target="_blank" class="btn btn-outline-secondary" title="Pollution"><i
                                                    class="fas fa-smog"></i></a>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Registration</small>
                                        <span class="font-weight-bold">
                                            <?php echo e($car['r_year']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <?php if ($car['r_status'] !== 'approve'): ?>
                                        <a href="cars.php?action=approve&id=<?php echo $car['r_id']; ?>"
                                            class="btn btn-success flex-grow-1">Approve Listing</a>
                                    <?php else: ?>
                                        <a href="cars.php?action=disapprove&id=<?php echo $car['r_id']; ?>"
                                            class="btn btn-outline-danger flex-grow-1">Hide Listing</a>
                                    <?php endif; ?>
                                    <button class="btn btn-light"><i class="fas fa-trash text-danger"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .btn-white {
        background: white;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .btn-white.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .uppercase {
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }
</style>

<?php include '../templates/header.php'; ?>