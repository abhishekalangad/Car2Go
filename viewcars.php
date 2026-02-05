<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Force login to view cars
require_login();

$l_id = $_SESSION['l_id'];
$u_pincode = 0;

// Get user pincode for distance calculation
$user_query = "SELECT u_pincode FROM user_reg WHERE ul_id = ?";
$user_data = db_fetch_one($con, $user_query, "i", [$l_id]);
if ($user_data) {
  $u_pincode = $user_data['u_pincode'];
}

// Search Logic
$cars = [];
$search_performed = false;

if (isset($_POST['search'])) {
  $km = (int) $_POST['km'];
  $hpin = $u_pincode - $km;
  $lpin = $u_pincode + $km;

  $search_query = "SELECT * FROM rent WHERE r_status = 'approve' AND (r_pincode BETWEEN ? AND ?)";
  $cars = db_fetch_all($con, $search_query, "ii", [$hpin, $lpin]);
  $search_performed = true;
} else {
  // Show all approved cars by default
  $cars = db_fetch_all($con, "SELECT * FROM rent WHERE r_status = 'approve' LIMIT 12");
}

$page_title = 'Find Your Perfect Car - CAR2GO';
include 'templates/header.php';
?>

<style>
  .search-section {
    background: var(--bg-dark);
    padding: 60px 0;
    margin-top: -20px;
  }

  .car-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f1f5f9;
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .car-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  }

  .car-img-wrapper {
    position: relative;
    height: 200px;
    overflow: hidden;
  }

  .car-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }

  .car-card:hover .car-img-wrapper img {
    transform: scale(1.1);
  }

  .car-price-tag {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: var(--primary-color);
    color: white;
    padding: 5px 15px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.9rem;
  }

  .car-body {
    padding: 1.5rem;
    flex-grow: 1;
  }

  .car-title {
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: var(--bg-dark);
  }

  .car-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 1rem;
    color: #64748b;
    font-size: 0.85rem;
  }

  .star-rating {
    color: #fbbf24;
    margin-bottom: 1rem;
  }

  .btn-view {
    width: 100%;
    border-radius: 12px;
    padding: 10px;
    font-weight: 600;
  }
</style>

<!-- Search Filter -->
<div class="search-section">
  <div class="container">
    <div class="glass-card" style="padding: 2.5rem;">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h2 class="text-white font-weight-bold mb-1">Discover Luxury</h2>
          <p class="text-white-50 mb-0">Search premium vehicles near your location (PIN: <?php echo $u_pincode; ?>).</p>
        </div>
        <div class="col-lg-6">
          <form action="viewcars.php" method="POST"
            class="row no-gutters shadow-sm rounded-lg overflow-hidden border border-secondary">
            <div class="col-8">
              <select name="km" class="form-control border-0 rounded-0 py-4 h-auto shadow-none bg-white">
                <option value="10">Within 10 km</option>
                <option value="20">Within 20 km</option>
                <option value="50">Within 50 km</option>
                <option value="100">Within 100 km</option>
                <option value="100000">Show All Available Cars</option>
              </select>
            </div>
            <div class="col-4">
              <button type="submit" name="search"
                class="btn btn-premium btn-gradient btn-block rounded-0 py-4 font-weight-bold h-100">
                <i class="fas fa-search"></i>
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
      <div>
        <h2 class="font-weight-bold mb-1"><?php echo $search_performed ? 'Search Results' : 'Available Vehicles'; ?>
        </h2>
        <p class="text-muted">Explore our curated collection of premium cars</p>
      </div>
      <div class="badge badge-primary px-3 py-2 rounded-pill">
        <?php echo count($cars); ?> Cars Found
      </div>
    </div>

    <div class="row">
      <?php if (empty($cars)): ?>
        <div class="col-12 text-center py-5">
          <i class="fas fa-car-side fa-4x text-muted mb-3 opacity-3"></i>
          <h3>No cars found in this range</h3>
          <p class="text-muted">Try increasing your search distance.</p>
        </div>
      <?php else: ?>
        <?php foreach ($cars as $car): ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="car-card shadow-sm">
              <div class="car-img-wrapper">
                <img src="images/<?php echo e($car['r_car']); ?>" alt="<?php echo e($car['r_mname']); ?>">
                <div class="car-price-tag">₹<?php echo e($car['rent_amt']); ?>/day</div>
              </div>
              <div class="car-body">
                <h3 class="car-title"><?php echo e($car['r_company']); ?>     <?php echo e($car['r_mname']); ?></h3>
                <div class="car-meta">
                  <span><i class="fas fa-calendar-alt mr-1"></i> <?php echo e($car['r_year']); ?></span>
                  <span><i class="fas fa-users mr-1"></i> <?php echo e($car['r_seat']); ?> Seats</span>
                  <span><i class="fas fa-gas-pump mr-1"></i> ₹<?php echo e($car['r_ppkm']); ?>/km</span>
                </div>

                <div class="star-rating">
                  <?php
                  $rating_query = "SELECT AVG(rating) as avg_rate, COUNT(*) as count FROM rating WHERE l_id = ?";
                  $rate_data = db_fetch_one($con, $rating_query, "i", [$car['rl_id']]);
                  $avg_rate = round($rate_data['avg_rate'] ?? 0);

                  if ($rate_data['count'] > 0) {
                    for ($i = 1; $i <= 5; $i++) {
                      echo '<i class="' . ($i <= $avg_rate ? 'fas' : 'far') . ' fa-star"></i>';
                    }
                    echo ' <small class="text-muted ml-1">(' . $rate_data['count'] . ')</small>';
                  } else {
                    echo '<span class="text-muted small">No ratings yet</span>';
                  }
                  ?>
                </div>

                <div class="d-flex gap-2 mt-3">
                  <a href="viewcarprofile.php?r_id=<?php echo e($car['r_id']); ?>"
                    class="btn btn-primary btn-view flex-grow-1 mr-2 px-4 shadow-sm">
                    View Details
                  </a>
                  <a href="viewreviews.php?r_id=<?php echo e($car['rl_id']); ?>"
                    class="btn btn-outline-secondary btn-view shadow-none" title="View Reviews">
                    <i class="far fa-comments"></i>
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