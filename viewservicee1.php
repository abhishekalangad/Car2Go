<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Force login
require_login();

$l_id = $_SESSION['l_id'];
$u_pincode = 0;

// Get user pincode
$user_data = db_fetch_one($con, "SELECT u_pincode FROM user_reg WHERE ul_id = ?", "i", [$l_id]);
if ($user_data) {
  $u_pincode = $user_data['u_pincode'];
}

$service_centers = [];
$search_performed = false;

if (isset($_POST['search'])) {
  $km = (int) $_POST['km'];
  $hpin = $u_pincode - $km;
  $lpin = $u_pincode + $km;

  $query = "SELECT * FROM service_reg WHERE (s_pincode BETWEEN ? AND ?)";
  $service_centers = db_fetch_all($con, $query, "ii", [$hpin, $lpin]);
  $search_performed = true;
} else {
  // Show all centers by default
  $service_centers = db_fetch_all($con, "SELECT * FROM service_reg LIMIT 12");
}

$page_title = 'Find Service Centers - CAR2GO';
include 'templates/header.php';
?>

<style>
  .search-section {
    background: var(--bg-dark);
    padding: 60px 0;
    margin-top: -20px;
  }

  .service-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f1f5f9;
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  }

  .center-img-wrapper {
    position: relative;
    height: 180px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .center-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .service-body {
    padding: 1.5rem;
    flex-grow: 1;
  }

  .center-title {
    font-weight: 700;
    font-size: 1.2rem;
    color: var(--bg-dark);
    margin-bottom: 0.5rem;
  }

  .center-meta {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 1rem;
  }

  .status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #22c55e;
    color: white;
    padding: 4px 12px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
  }
</style>

<!-- Search Section -->
<div class="search-section">
  <div class="container">
    <div class="glass-card" style="padding: 2rem;">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h2 class="text-white font-weight-bold mb-1">Expert Vehicle Care</h2>
          <p class="text-white-50 mb-0">Find certified service centers near your location.</p>
        </div>
        <div class="col-lg-6">
          <form action="" method="POST" class="row no-gutters shadow-sm rounded-lg overflow-hidden">
            <div class="col-8">
              <select name="km" class="form-control border-0 rounded-0 py-4 h-auto shadow-none">
                <option value="10">Within 10 km (My PIN: <?php echo $u_pincode; ?>)</option>
                <option value="20">Within 20 km</option>
                <option value="50">Within 50 km</option>
                <option value="100">Within 100 km</option>
                <option value="100000">Show All Centers</option>
              </select>
            </div>
            <div class="col-4">
              <button type="submit" name="search"
                class="btn btn-primary btn-block rounded-0 py-4 font-weight-bold h-100">
                SEARCH
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Results Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-5">
      <h3 class="font-weight-bold">
        <?php echo $search_performed ? 'Search Results' : 'Recommended Centers'; ?>
      </h3>
      <span class="text-muted"><?php echo count($service_centers); ?> centers found</span>
    </div>

    <div class="row">
      <?php if (empty($service_centers)): ?>
        <div class="col-12 text-center py-5">
          <div class="mb-4"><i class="fas fa-tools fa-4x text-muted opacity-3"></i></div>
          <h4 class="text-muted">No service centers found in this range.</h4>
          <p>Try searching with a wider radius.</p>
        </div>
      <?php else: ?>
        <?php foreach ($service_centers as $sc): ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="service-card shadow-sm">
              <div class="center-img-wrapper">
                <img src="uploads/services/<?php echo e($sc['s_rc'] ?: 'default-service.jpg'); ?>" alt="Service Center">
                <span class="status-badge">Certified</span>
              </div>
              <div class="service-body">
                <h4 class="center-title"><?php echo e($sc['s_name']); ?></h4>
                <div class="center-meta">
                  <div class="mb-1"><i class="fas fa-map-marker-alt mr-2 text-primary"></i>
                    <?php echo e($sc['s_address']); ?></div>
                  <div class="mb-1"><i class="fas fa-phone mr-2 text-primary"></i> <?php echo e($sc['s_phone']); ?></div>
                  <div><i class="fas fa-hashtag mr-2 text-primary"></i> PIN: <?php echo e($sc['s_pincode']); ?></div>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="text-warning small">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                    <span class="text-muted ml-1">(4.5)</span>
                  </div>
                  <a href="viewserviceprofile.php?sl_id=<?php echo $sc['sl_id']; ?>"
                    class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    View Center
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'templates/footer.php'; ?>