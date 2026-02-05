<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Ensure user is logged in
require_login();

$error_message = '';
$success_message = '';
$l_id = $_SESSION['l_id'];

if (isset($_POST['submit'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid request. Please try again.';
    } else {
        // Sanitize text inputs
        $company = sanitize_input($_POST['company']);
        $mname = sanitize_input($_POST['mname']);
        $year = sanitize_input($_POST['year']);
        $number = sanitize_input($_POST['number']);
        $ppkm = sanitize_input($_POST['ppkm']);
        $rseat = sanitize_input($_POST['rseat']);
        $rent_amt = sanitize_input($_POST['rent']);
        $pincode = sanitize_input($_POST['pincode']);
        $phone = sanitize_input($_POST['phone']);
        $addinfo = sanitize_input($_POST['addinfo']);
        $custatus = sanitize_input($_POST['custatus']);
        $acchistory = sanitize_input($_POST['acchistory']);
        $approve = 'not approve';

        // Validation
        if (!validate_phone($phone)) {
            $error_message = 'Invalid phone number';
        } else if (!validate_pincode($pincode)) {
            $error_message = 'Invalid pincode';
        } else {
            // Validate all 4 file uploads
            $files_to_check = [
                'car' => ['label' => 'Car Photo', 'dir' => 'uploads/cars/'],
                'tax' => ['label' => 'RC Book/Tax', 'dir' => 'uploads/documents/'],
                'insurance' => ['label' => 'Insurance', 'dir' => 'uploads/documents/'],
                'polution' => ['label' => 'Pollution Cert', 'dir' => 'uploads/documents/']
            ];

            $uploaded_filenames = [];
            $upload_ok = true;

            foreach ($files_to_check as $key => $info) {
                $check = validate_file_upload($_FILES[$key], ['image/jpeg', 'image/png', 'application/pdf'], 3 * 1024 * 1024);
                if ($check !== true) {
                    $error_message = $info['label'] . ": " . $check;
                    $upload_ok = false;
                    break;
                }
                $uploaded_filenames[$key] = generate_unique_filename($_FILES[$key]['name']);
            }

            if ($upload_ok) {
                // Database Insert
                $query = "INSERT INTO rent (rl_id, r_company, r_mname, r_year, r_number, r_addinfo, r_custatus, r_acchistory, r_car, r_tax, r_insurance, r_polution, r_ppkm, r_status, r_seat, r_pincode, r_phone, rent_amt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $result = db_execute($con, $query, "issssssssssssssssi", [
                    $l_id,
                    $company,
                    $mname,
                    $year,
                    $number,
                    $addinfo,
                    $custatus,
                    $acchistory,
                    $uploaded_filenames['car'],
                    $uploaded_filenames['tax'],
                    $uploaded_filenames['insurance'],
                    $uploaded_filenames['polution'],
                    $ppkm,
                    $approve,
                    $rseat,
                    $pincode,
                    $phone,
                    $rent_amt
                ]);

                if ($result) {
                    // Update login status
                    $update_login = "UPDATE login SET l_approve = ? WHERE l_id = ?";
                    db_execute($con, $update_login, "si", [$approve, $l_id]);

                    // Move files
                    foreach ($files_to_check as $key => $info) {
                        if (!is_dir($info['dir']))
                            mkdir($info['dir'], 0755, true);
                        move_uploaded_file($_FILES[$key]['tmp_name'], $info['dir'] . $uploaded_filenames[$key]);
                    }

                    $success_message = 'Car successfully listed! Waiting for admin verification.';
                } else {
                    $error_message = 'Failed to save car details to database.';
                }
            }
        }
    }
}

$page_title = 'List Your Car - CAR2GO';
include 'templates/header.php';
?>

<div class="hero-section" style="height: auto; padding: 100px 0;">
    <div class="container d-flex justify-content-center">
        <div class="glass-card" style="width: 100%; max-width: 900px; padding: 40px;">
            <div class="text-center mb-5">
                <h2 class="display-4 font-weight-bold" style="color: white; letter-spacing: -2px;">Rent Your
                    <span>Vehicle</span></h2>
                <p class="text-white-50">Provide car details and documents for verification.</p>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-danger mb-4"><?php echo e($error_message); ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success mb-4"><?php echo e($success_message); ?></div>
            <?php endif; ?>

            <form action="#" method="post" enctype="multipart/form-data" class="premium-form">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-white small mb-1">Company Name</label>
                        <input type="text" class="form-control bg-transparent text-white border-secondary"
                            name="company" placeholder="e.g. Toyota" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white small mb-1">Model Name</label>
                        <input type="text" class="form-control bg-transparent text-white border-secondary" name="mname"
                            placeholder="e.g. Camry" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white small mb-1">Model Year</label>
                        <input type="text" class="form-control bg-transparent text-white border-secondary" name="year"
                            placeholder="e.g. 2022" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-white small mb-1">Reg. Number</label>
                        <input type="text" class="form-control bg-transparent text-white border-secondary" name="number"
                            placeholder="XX-00-XX-0000" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white small mb-1">Rent/KM (₹)</label>
                        <input type="number" class="form-control bg-transparent text-white border-secondary" name="ppkm"
                            required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white small mb-1">Daily Rent (₹)</label>
                        <input type="number" class="form-control bg-transparent text-white border-secondary" name="rent"
                            required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-white small mb-1">Seating Capacity</label>
                        <input type="number" class="form-control bg-transparent text-white border-secondary"
                            name="rseat" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white small mb-1">Location Pincode</label>
                        <input type="text" class="form-control bg-transparent text-white border-secondary"
                            name="pincode" maxlength="6" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white small mb-1">Contact Phone</label>
                        <input type="text" class="form-control bg-transparent text-white border-secondary" name="phone"
                            required pattern="[0-9]{10}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="text-white small mb-1">Additional Information</label>
                        <textarea name="addinfo" class="form-control bg-transparent text-white border-secondary"
                            rows="2"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white small mb-1">Mechanical Status</label>
                        <textarea name="custatus" class="form-control bg-transparent text-white border-secondary"
                            rows="2" placeholder="Engine, AC, Tyres condition..."></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-white small mb-1">Accident History</label>
                        <textarea name="acchistory" class="form-control bg-transparent text-white border-secondary"
                            rows="2" placeholder="Describe any past accidents..."></textarea>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="p-3 border border-secondary rounded text-center h-100"
                            style="background: rgba(0,0,0,0.2);">
                            <label class="text-white small font-weight-bold d-block mb-3">Car Photo</label>
                            <i class="fas fa-camera fa-2x text-primary mb-3"></i>
                            <input type="file" name="car" class="small text-white-50 w-100" required>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="p-3 border border-secondary rounded text-center h-100"
                            style="background: rgba(0,0,0,0.2);">
                            <label class="text-white small font-weight-bold d-block mb-3">RC Book</label>
                            <i class="fas fa-file-contract fa-2x text-primary mb-3"></i>
                            <input type="file" name="tax" class="small text-white-50 w-100" required>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="p-3 border border-secondary rounded text-center h-100"
                            style="background: rgba(0,0,0,0.2);">
                            <label class="text-white small font-weight-bold d-block mb-3">Insurance</label>
                            <i class="fas fa-shield-alt fa-2x text-primary mb-3"></i>
                            <input type="file" name="insurance" class="small text-white-50 w-100" required>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="p-3 border border-secondary rounded text-center h-100"
                            style="background: rgba(0,0,0,0.2);">
                            <label class="text-white small font-weight-bold d-block mb-3">Pollution Cert</label>
                            <i class="fas fa-smog fa-2x text-primary mb-3"></i>
                            <input type="file" name="polution" class="small text-white-50 w-100" required>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" name="submit" class="btn btn-premium btn-gradient px-5 py-3">Submit Vehicle
                        Listing</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .premium-form .form-control:focus {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 10px rgba(37, 99, 235, 0.2);
        color: white !important;
    }
</style>

<?php include 'templates/footer.php'; ?>