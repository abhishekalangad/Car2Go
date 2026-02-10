<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();

$l_id = $_SESSION['l_id'];
$sl_id = isset($_GET['sl_id']) ? (int) $_GET['sl_id'] : 0;
$error_message = '';
$success_message = '';

if ($sl_id <= 0 && !isset($_POST['rating'])) {
  redirect_with_message('viewservice.php', 'Invalid service selection.', 'danger');
}

// Handle Form Submission
if (isset($_POST['rating'])) {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $error_message = 'Session invalid. Please try again.';
  } else {
    $rating = isset($_POST['rate']) ? (int) $_POST['rate'] : 0;
    $review = sanitize_input($_POST['rev']);
    $target_id = (int) $_POST['rid'];

    if ($rating < 1 || $rating > 5) {
      $error_message = 'Please select a star rating.';
    } else {
      // Note: column name is 'u_id' for user id, 'sl_id' for service id
      $query = "INSERT INTO srating (rating, review, u_id, sl_id) VALUES (?, ?, ?, ?)";
      if (db_execute($con, $query, "isii", [$rating, $review, $l_id, $target_id])) {
        redirect_with_message('uviewservh.php', 'Thank you! Your feedback on the service has been submitted.', 'success');
      } else {
        $error_message = "Failed to submit review. You may have already reviewed this service.";
      }
    }
  }
  // retain ID if failed
  $sl_id = (int) $_POST['rid'];
}

// Fetch Service Center Details for Display
$service_query = "SELECT s_name, s_image1 as s_image FROM service_reg WHERE sl_id = ?";
$service = db_fetch_one($con, $service_query, "i", [$sl_id]);

if (!$service) {
  // Service not found
}

$service_name = $service ? $service['s_name'] : 'Service Center';

$page_title = 'Rate Service - CAR2GO';
include 'templates/header.php';
?>

<div class="page-hero">
  <div class="container hero-content text-center">
    <h1 class="font-weight-bold mb-2 animate__animated animate__fadeInDown">How Was The Service?</h1>
    <p class="lead text-white-50 animate__animated animate__fadeInUp">Share your experience with
      <?php echo htmlspecialchars($service_name); ?>.</p>
  </div>
</div>

<div class="container animate__animated animate__fadeInUp animate__delay-1s"
  style="margin-top: -80px; position: relative; z-index: 10;">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
        <div class="card-body p-5 text-center">

          <?php if (!empty($service['s_image'])): ?>
            <img src="uploads/services/<?php echo htmlspecialchars($service['s_image']); ?>"
              onerror="this.src='images/service_icon.png'" class="rounded shadow-sm mb-4"
              style="width: 120px; height: 80px; object-fit: cover; border: 4px solid #fff;">
          <?php else: ?>
            <div
              class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-4 text-primary font-weight-bold shadow-sm"
              style="width: 100px; height: 100px; font-size: 2rem; border: 4px solid #fff;">
              <i class="fas fa-tools"></i>
            </div>
          <?php endif; ?>

          <h4 class="font-weight-bold text-dark mb-1"><?php echo htmlspecialchars($service_name); ?></h4>
          <p class="text-muted small mb-4">Service Center</p>

          <?php if ($error_message): ?>
            <div class="alert alert-danger small mb-4"><?php echo $error_message; ?></div>
          <?php endif; ?>

          <form action="ratings.php" method="POST" class="text-left">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="rid" value="<?php echo $sl_id; ?>">

            <div class="mb-4 text-center">
              <label class="d-block small font-weight-bold text-uppercase text-muted mb-2">Tap stars to rate</label>
              <div class="rate-stars d-inline-block">
                <input type="radio" id="star5" name="rate" value="5" /><label for="star5" title="Excellent">5
                  stars</label>
                <input type="radio" id="star4" name="rate" value="4" /><label for="star4" title="Good">4 stars</label>
                <input type="radio" id="star3" name="rate" value="3" /><label for="star3" title="Average">3
                  stars</label>
                <input type="radio" id="star2" name="rate" value="2" /><label for="star2" title="Poor">2 stars</label>
                <input type="radio" id="star1" name="rate" value="1" /><label for="star1" title="Terrible">1
                  star</label>
              </div>
            </div>

            <div class="form-group mb-4">
              <label class="small font-weight-bold text-muted text-uppercase">Write a Review</label>
              <textarea name="rev" class="form-control bg-light border-0" rows="4"
                placeholder="How was the quality of service, time taken, and pricing?..." required></textarea>
            </div>

            <button type="submit" name="rating"
              class="btn btn-primary btn-block btn-lg rounded-pill font-weight-bold shadow-sm btn-gradient">
              SUBMIT REVIEW
            </button>
          </form>
        </div>
      </div>

      <div class="text-center mt-4">
        <a href="uviewservh.php" class="text-muted small"><i class="fas fa-arrow-left mr-1"></i> Back to My Service
          History</a>
      </div>
    </div>
  </div>
</div>

<style>
  .page-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 5rem 0 10rem;
    color: white;
    position: relative;
    overflow: hidden;
  }

  /* Star Rating CSS */
  .rate-stars {
    height: 50px;
    padding: 0 10px;
  }

  .rate-stars:not(:checked)>input {
    position: absolute;
    top: -9999px;
  }

  .rate-stars:not(:checked)>label {
    float: right;
    width: 1em;
    overflow: hidden;
    white-space: nowrap;
    cursor: pointer;
    font-size: 35px;
    color: #cbd5e1;
    margin: 0 2px;
    transition: color 0.2s;
  }

  .rate-stars:not(:checked)>label:before {
    content: '★ ';
  }

  .rate-stars>input:checked~label {
    color: #fbbf24;
  }

  .rate-stars:not(:checked)>label:hover,
  .rate-stars:not(:checked)>label:hover~label {
    color: #fcd34d;
  }

  .rate-stars>input:checked+label:hover,
  .rate-stars>input:checked+label:hover~label,
  .rate-stars>input:checked~label:hover,
  .rate-stars>input:checked~label:hover~label,
  .rate-stars>label:hover~input:checked~label {
    color: #fbbf24;
  }

  .btn-gradient {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border: none;
    transition: transform 0.2s;
  }

  .btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
  }
</style>

<?php include 'templates/footer.php'; ?>