<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();

$rl_id = isset($_GET['r_id']) ? (int) $_GET['r_id'] : 0;

if ($rl_id <= 0) {
  redirect_with_message('viewcars.php', 'Invalid selection.', 'danger');
}

// Fetch Reviews with User Details
$query = "SELECT r.*, u.u_name 
          FROM rating r 
          JOIN user_reg u ON r.ur_id = u.ul_id 
          WHERE r.l_id = ? 
          ORDER BY r.id DESC";
$reviews = db_fetch_all($con, $query, "i", [$rl_id]);

// Get Target Name (User/Driver/Service)
$target_query = "SELECT u_name FROM user_reg WHERE ul_id = ?";
$target = db_fetch_one($con, $target_query, "i", [$rl_id]);

if (!$target) {
  // Check if it's a driver
  $target = db_fetch_one($con, "SELECT d_name as u_name FROM driver_reg WHERE dl_id = ?", "i", [$rl_id]);
}
if (!$target) {
  // Check if it's a service center
  $target = db_fetch_one($con, "SELECT s_name as u_name FROM service_reg WHERE sl_id = ?", "i", [$rl_id]);
}

$target_name = $target ? $target['u_name'] : 'Professional';

$page_title = 'User Reviews for ' . $target_name;
include 'templates/header.php';
?>

<style>
  .review-header {
    background: var(--bg-dark);
    padding: 80px 0;
    margin-top: -20px;
    color: white;
    text-align: center;
  }

  .review-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    border: 1px solid #f1f5f9;
    transition: all 0.3s ease;
    position: relative;
  }

  .review-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
  }

  .quote-icon {
    position: absolute;
    top: 20px;
    right: 30px;
    font-size: 3rem;
    color: #f1f5f9;
    z-index: 0;
  }

  .review-content {
    position: relative;
    z-index: 1;
  }

  .user-avatar {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
  }

  .rating-stars {
    color: #fbbf24;
    margin-bottom: 1rem;
  }
</style>

<div class="review-header">
  <div class="container">
    <h1 class="font-weight-bold display-4 mb-3">Customer <span class="text-primary">Reviews</span></h1>
    <p class="lead opacity-7">Honest feedback from our community for <?php echo e($target_name); ?>.</p>
  </div>
</div>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <?php if (empty($reviews)): ?>
        <div class="text-center py-5">
          <div class="mb-4"><i class="far fa-comments fa-5x text-muted opacity-2"></i></div>
          <h3 class="text-muted font-weight-bold">No reviews yet</h3>
          <p class="text-muted">Be the first one to share your experience!</p>
        </div>
      <?php else: ?>
        <?php foreach ($reviews as $rev): ?>
          <div class="review-card shadow-sm">
            <i class="fas fa-quote-right quote-icon"></i>
            <div class="review-content">
              <div class="rating-stars">
                <?php
                $stars = (int) $rev['rating'];
                for ($i = 1; $i <= 5; $i++) {
                  echo '<i class="' . ($i <= $stars ? 'fas' : 'far') . ' fa-star"></i>';
                }
                ?>
                <span class="ml-2 font-weight-bold text-dark"><?php echo $stars; ?>.0</span>
              </div>
              <blockquote class="h5 font-italic text-muted mb-4 leading-relaxed">
                "<?php echo nl2br(e($rev['review'])); ?>"
              </blockquote>
              <hr>
              <div class="d-flex align-items-center mt-4">
                <div class="user-avatar mr-3">
                  <?php echo strtoupper(substr($rev['u_name'], 0, 1)); ?>
                </div>
                <div>
                  <h6 class="font-weight-bold mb-0"><?php echo e($rev['u_name']); ?></h6>
                  <small class="text-muted">Verified Customer</small>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<style>
  .leading-relaxed {
    line-height: 1.8;
  }
</style>

<?php include 'templates/header.php'; ?>