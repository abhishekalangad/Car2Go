<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();

$l_id = $_SESSION['l_id'];
$b_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($b_id <= 0) {
  redirect_with_message('user/dashboard.php', 'Invalid booking.', 'danger');
}

// Fetch booking and verify ownership
$query = "SELECT b.*, r.r_company, r.r_mname, r.rent_amt 
          FROM bookcar b 
          JOIN rent r ON b.br_id = r.r_id 
          WHERE b.b_id = ? AND b.bo_id = ?";
$booking = db_fetch_one($con, $query, "ii", [$b_id, $l_id]);

if (!$booking) {
  redirect_with_message('user/dashboard.php', 'Booking not found or access denied.', 'danger');
}

// Calculate days (simple difference)
$start = strtotime($booking['b_day1']);
$end = strtotime($booking['b_day2']);
$days = max(1, ceil(($end - $start) / (60 * 60 * 24)));
$total_amt = $days * $booking['rent_amt'];

$error_message = '';
$success_message = '';

if (isset($_POST['process_payment'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Session expired. Please try again.';
  } else {
    // Mock payment processing
    $update_query = "UPDATE bookcar SET payment = ?, b_status = 'Paid' WHERE b_id = ?";
    if (db_execute($con, $update_query, "si", ['Processed', $b_id])) {
      redirect_with_message('user/dashboard.php', 'Payment successful! Your vehicle is now confirmed.', 'success');
    } else {
      $error_message = 'Payment failed. Please try again later.';
    }
  }
}

$page_title = 'Secure Payment - CAR2GO';
include 'templates/header.php';
?>

<div class="hero-section py-5" style="background: var(--bg-dark); color: white;">
  <div class="container text-center">
    <h2 class="font-weight-bold">Secure <span class="text-primary">Checkout</span></h2>
    <p class="opacity-7">Complete your payment for <?php echo e($booking['r_company']); ?>
      <?php echo e($booking['r_mname']); ?></p>
  </div>
</div>

<div class="container py-5 mt-n5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
        <div class="card-body p-5">
          <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo e($error_message); ?></div>
          <?php endif; ?>

          <div class="d-flex justify-content-between mb-4">
            <span class="text-muted">Daily Rate</span>
            <span class="font-weight-bold">₹<?php echo e($booking['rent_amt']); ?></span>
          </div>
          <div class="d-flex justify-content-between mb-4 border-bottom pb-4">
            <span class="text-muted">Rental Duration</span>
            <span class="font-weight-bold"><?php echo $days; ?> Days</span>
          </div>
          <div class="d-flex justify-content-between mb-5 h4">
            <span class="font-weight-bold">Total Amount</span>
            <span class="font-weight-bold text-primary">₹<?php echo number_format($total_amt, 2); ?></span>
          </div>

          <form action="" method="post" class="premium-form text-dark">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group mb-4">
              <label class="small font-weight-bold text-muted uppercase">Card Number</label>
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
                <label class="small font-weight-bold text-muted uppercase">Expiry Date</label>
                <input type="text" class="form-control bg-light border-0" placeholder="MM / YY">
              </div>
              <div class="col-5 form-group mb-4">
                <label class="small font-weight-bold text-muted uppercase">CVV</label>
                <input type="password" class="form-control bg-light border-0" placeholder="***">
              </div>
            </div>

            <button type="submit" name="process_payment" class="btn btn-premium btn-gradient w-100 py-3 shadow">
              PAY ₹<?php echo number_format($total_amt, 2); ?> NOW
            </button>

            <div class="text-center mt-4">
              <img
                src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/2560px-Visa_Inc._logo.svg.png"
                height="20" class="mr-3 opacity-5">
              <img
                src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1280px-Mastercard-logo.svg.png"
                height="20" class="opacity-5">
              <p class="small text-muted mt-3"><i class="fas fa-lock mr-1"></i> Your payment information is never
                stored.</p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>