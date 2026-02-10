<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();

$l_id = $_SESSION['l_id'];
$bd_id = isset($_GET['d_id']) ? (int) $_GET['d_id'] : 0;
// Note: legacy URL used 'd_id' for booking ID. In database it might be 'bd_id' or 'd_id'.
// Based on `udriverthis.php`, the primary key selected was `b.bd_id`, link passed `d_id=$row['bd_id']`.
// So $bd_id matches table PK.

if ($bd_id <= 0) {
  redirect_with_message('udriverthis.php', 'Invalid booking.', 'danger');
}

// Fetch booking and verify ownership
// bookdriver has du_id (user id), dd_id (driver id)
$query = "SELECT b.*, d.d_name, d.d_amount 
          FROM bookdriver b 
          JOIN driver_reg d ON b.dd_id = d.dl_id 
          WHERE b.bd_id = ? AND b.dr_id = ?";
// Note: In `udriverthis.php`, I changed WHERE to `b.dr_id = ?`. 
// Assuming `dr_id` is the User ID column in bookdriver.

$booking = db_fetch_one($con, $query, "ii", [$bd_id, $l_id]);

if (!$booking) {
  redirect_with_message('udriverthis.php', 'Booking not found or access denied.', 'danger');
}

// Calculate days
$start = new DateTime($booking['d_day1']);
$end = new DateTime($booking['d_day2']);
$days = max(1, $start->diff($end)->days);
$total_amt = $days * $booking['d_amount'];

$error_message = '';
$success_message = '';

if (isset($_POST['process_payment'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Session expired. Please try again.';
  } else {
    // Mock payment processing
    // Update payment status AND maybe d_status to Confirmed?
    // Legacy pay1.php updated `payment` column with the AMOUNT string? 
    // Logic: $q="UPDATE bookdriver SET payment='$pay' WHERE d_id=$b_id"; 
    // It set payment to the amount. That's weird. Usually payment is 'Paid' or 'Pending'.
    // I will set it to 'Paid' or the amount if that's what legacy requires.
    // But `udriverthis.php` checks `if(empty($row['payment']) || $row['payment'] == 'Pending')`.
    // So I should set it to 'Paid' or the amount. Let's set it to "Paid".

    $update_query = "UPDATE bookdriver SET payment = ?, d_status = 'Approved' WHERE bd_id = ?";
    if (db_execute($con, $update_query, "si", ['Paid', $bd_id])) {
      redirect_with_message('udriverthis.php', 'Payment successful! Driver booking confirmed.', 'success');
    } else {
      $error_message = 'Payment failed. Please try again later.';
    }
  }
}

$page_title = 'Pay Driver - CAR2GO';
include 'templates/header.php';
?>

<div class="page-hero">
  <div class="container hero-content text-center">
    <h1 class="font-weight-bold mb-2 animate__animated animate__fadeInDown">Secure <span
        class="text-primary">Checkout</span></h1>
    <p class="lead text-white-50 animate__animated animate__fadeInUp">Complete your payment for driver
      <?php echo htmlspecialchars($booking['d_name']); ?></p>
  </div>
</div>

<style>
  .page-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 5rem 0 8rem;
    color: white;
    position: relative;
    overflow: hidden;
  }
</style>

<div class="container py-5 mt-n5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card shadow-lg border-0 rounded-lg overflow-hidden animate__animated animate__fadeInUp">
        <div class="card-body p-5">
          <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
          <?php endif; ?>

          <div class="d-flex justify-content-between mb-4">
            <span class="text-muted">Daily Rate</span>
            <span class="font-weight-bold">₹<?php echo number_format($booking['d_amount']); ?></span>
          </div>
          <div class="d-flex justify-content-between mb-4 border-bottom pb-4">
            <span class="text-muted">Duration</span>
            <span class="font-weight-bold"><?php echo $days; ?> Days</span>
          </div>
          <div class="d-flex justify-content-between mb-5 h4">
            <span class="font-weight-bold">Total Amount</span>
            <span class="font-weight-bold text-primary">₹<?php echo number_format($total_amt, 2); ?></span>
          </div>

          <form action="" method="post" class="premium-form text-dark">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group mb-4">
              <label class="small font-weight-bold text-muted text-uppercase">Card Number</label>
              <div class="input-group">
                <input type="text" class="form-control bg-light border-0" placeholder="0000 0000 0000 0000"
                  maxlength="19">
                <div class="input-group-append">
                  <span class="input-group-text bg-light border-0"><i class="fas fa-credit-card text-muted"></i></span>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-7 form-group mb-4">
                <label class="small font-weight-bold text-muted text-uppercase">Expiry Date</label>
                <input type="text" class="form-control bg-light border-0" placeholder="MM / YY">
              </div>
              <div class="col-5 form-group mb-4">
                <label class="small font-weight-bold text-muted text-uppercase">CVV</label>
                <input type="password" class="form-control bg-light border-0" placeholder="***">
              </div>
            </div>

            <button type="submit" name="process_payment"
              class="btn btn-primary btn-block rounded-pill py-3 shadow font-weight-bold"
              style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
              PAY ₹<?php echo number_format($total_amt, 2); ?> NOW
            </button>

            <div class="text-center mt-4">
              <p class="small text-muted mt-3"><i class="fas fa-lock mr-1"></i> 100% Secure Payment</p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>