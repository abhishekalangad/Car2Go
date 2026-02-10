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
  $u_pincode = $user_data['u_pincode'] ?? 0;
}

// Search Logic
$cars = [];
$search_performed = false;

// Initialize filters
$km = (int) ($_POST['km'] ?? $_GET['km'] ?? 100000);
$min_price = (int) ($_POST['min_price'] ?? $_GET['min_price'] ?? 0);
$max_price = (int) ($_POST['max_price'] ?? $_GET['max_price'] ?? 10000);
$seats = (int) ($_POST['seats'] ?? $_GET['seats'] ?? 0);
$company = sanitize_input($_POST['company'] ?? $_GET['company'] ?? '');

$query = "SELECT * FROM rent WHERE r_status = 'approve'";
$params = [];
$types = "";

// Distance filter
if ($km < 100000) {
  $hpin = $u_pincode - $km;
  $lpin = $u_pincode + $km;
  $query .= " AND (r_pincode BETWEEN ? AND ?)";
  $params[] = $hpin;
  $params[] = $lpin;
  $types .= "ii";
}

// Price filter
if ($min_price > 0) {
  $query .= " AND rent_amt >= ?";
  $params[] = $min_price;
  $types .= "i";
}
if ($max_price < 10000) {
  $query .= " AND rent_amt <= ?";
  $params[] = $max_price;
  $types .= "i";
}

// Seats filter
if ($seats > 0) {
  if ($seats >= 7) {
    $query .= " AND r_seat >= 7";
  } else {
    $query .= " AND r_seat = ?";
    $params[] = $seats;
    $types .= "i";
  }
}

// Company filter
if (!empty($company)) {
  $query .= " AND r_company LIKE ?";
  $params[] = "%$company%";
  $types .= "s";
}

if (isset($_POST['search']) || isset($_GET['filter'])) {
  $cars = db_fetch_all($con, $query, $types, $params);
  $search_performed = true;
} else {
  // Show default approved cars
  $cars = db_fetch_all($con, $query . " LIMIT 12", $types, $params);
}

$page_title = 'Find Your Perfect Car - CAR2GO';
// Ensure navbar has background by NOT setting $no_padding
include 'templates/header.php';
?>

<style>
  /* Premium Page Styles */
  .page-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 5rem 0 8rem;
    /* Extra bottom padding for overlap */
    color: white;
    position: relative;
    overflow: hidden;
  }

  .page-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('images/bg3.jpg') center/cover;
    opacity: 0.15;
    z-index: 0;
  }

  .hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
  }

  /* Floating Search Card */
  .search-card-container {
    margin-top: -60px;
    position: relative;
    z-index: 10;
    margin-bottom: 4rem;
  }

  .glass-search {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.5);
  }

  /* Car Card Design */
  .car-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
  }

  .car-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: #3b82f6;
  }

  .car-img-wrapper {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .car-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }

  .car-card:hover .car-img-wrapper img {
    transform: scale(1.05);
    /* Subtle zoom */
  }

  .car-price-badge {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: rgba(15, 23, 42, 0.9);
    color: white;
    padding: 6px 14px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.9rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(4px);
  }

  .car-body {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }

  .car-title {
    font-weight: 800;
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
    color: #1e293b;
    letter-spacing: -0.5px;
  }

  .car-categories {
    font-size: 0.8rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    margin-bottom: 1rem;
  }

  .car-specs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 1.5rem;
    background: #f8fafc;
    padding: 15px;
    border-radius: 12px;
  }

  .spec-item {
    font-size: 0.85rem;
    color: #475569;
    display: flex;
    align-items: center;
  }

  .spec-item i {
    color: #3b82f6;
    width: 20px;
    margin-right: 5px;
  }

  .star-rating {
    color: #eab308;
    font-size: 0.9rem;
    margin-top: auto;
    padding-bottom: 1rem;
  }

  /* Form Controls */
  .form-control-lg {
    height: 50px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    font-size: 1rem;
  }

  .form-control-lg:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  .btn-search {
    height: 50px;
    border-radius: 10px;
    font-weight: 600;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border: none;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    transition: all 0.3s;
  }

  .btn-search:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(37, 99, 235, 0.4);
  }

  /* Helpers */
  .d-flex {
    display: flex;
  }

  .align-items-center {
    align-items: center;
  }

  .justify-content-between {
    justify-content: space-between;
  }

  .gap-2 {
    gap: 0.5rem;
  }

  /* Mobile Optimization */
  @media (max-width: 768px) {
    .page-hero {
      padding: 4rem 0 6rem;
    }

    .page-hero h1 {
      font-size: 2.2rem;
    }

    .search-card-container {
      margin-top: -40px;
    }

    .glass-search {
      padding: 1.5rem;
    }

    .car-card {
      margin-bottom: 2rem;
    }

    .car-img-wrapper {
      height: 200px;
    }

    .car-title {
      font-size: 1.1rem;
    }

    .car-specs {
      padding: 10px;
      gap: 10px;
    }
  }
</style>

<div class="main-content">

  <!-- Hero Section -->
  <div class="page-hero">
    <div class="container hero-content">
      <h1 class="font-weight-bold mb-2 animate__animated animate__fadeInDown">Find Your Perfect Drive</h1>
      <p class="lead text-white-50 animate__animated animate__fadeInUp">Premium fleet available for self-drive across
        the city</p>
    </div>
  </div>

  <!-- Floating Search Section -->
  <div class="container search-card-container animate__animated animate__fadeInUp animate__delay-1s">
    <div class="glass-search">
      <form action="viewcars.php" method="POST">
        <div class="row">
          <!-- Distance -->
          <div class="col-lg-3 col-md-6 mb-3">
            <label class="text-uppercase text-muted font-weight-bold small">Distance</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent border-right-0"><i
                    class="fas fa-map-marker-alt text-primary"></i></span>
              </div>
              <select name="km" class="form-control form-control-lg border-left-0">
                <option value="10" <?php echo $km == 10 ? 'selected' : ''; ?>>Within 10 km</option>
                <option value="20" <?php echo $km == 20 ? 'selected' : ''; ?>>Within 20 km</option>
                <option value="50" <?php echo $km == 50 ? 'selected' : ''; ?>>Within 50 km</option>
                <option value="100000" <?php echo $km == 100000 ? 'selected' : ''; ?>>All Locations</option>
              </select>
            </div>
          </div>

          <!-- Price Range -->
          <div class="col-lg-3 col-md-6 mb-3">
            <label class="text-uppercase text-muted font-weight-bold small">Max Daily Rent (₹)</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent border-right-0"><i
                    class="fas fa-wallet text-primary"></i></span>
              </div>
              <input type="number" name="max_price" class="form-control form-control-lg border-left-0"
                placeholder="Up to ₹10,000" value="<?php echo $max_price < 10000 ? $max_price : ''; ?>">
            </div>
          </div>

          <!-- Seats -->
          <div class="col-lg-2 col-md-6 mb-3">
            <label class="text-uppercase text-muted font-weight-bold small">Seats</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent border-right-0"><i
                    class="fas fa-users text-primary"></i></span>
              </div>
              <select name="seats" class="form-control form-control-lg border-left-0">
                <option value="0">Any</option>
                <option value="4" <?php echo $seats == 4 ? 'selected' : ''; ?>>4 Seats</option>
                <option value="5" <?php echo $seats == 5 ? 'selected' : ''; ?>>5 Seats</option>
                <option value="7" <?php echo $seats == 7 ? 'selected' : ''; ?>>7+ Seats</option>
              </select>
            </div>
          </div>

          <!-- Company -->
          <div class="col-lg-2 col-md-6 mb-3">
            <label class="text-uppercase text-muted font-weight-bold small">Brand</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent border-right-0"><i
                    class="fas fa-tag text-primary"></i></span>
              </div>
              <input type="text" name="company" class="form-control form-control-lg border-left-0"
                placeholder="e.g. Tata" value="<?php echo e($company); ?>">
            </div>
          </div>

          <div class="col-lg-2 col-md-12">
            <label class="d-none d-lg-block">&nbsp;</label>
            <button type="submit" name="search" class="btn btn-primary btn-block btn-search shadow-sm">
              <i class="fas fa-filter mr-1"></i> FILTER
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Listings Section -->
  <div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
      <h3 class="font-weight-bold text-dark mb-0">
        <?php echo $search_performed ? 'Search Results' : 'Available Vehicles'; ?>
      </h3>
      <span class="badge badge-light p-2" style="font-size: 0.9rem; color: #64748b;">
        <?php echo count($cars); ?> vehicles found
      </span>
    </div>

    <div class="row">
      <?php if (empty($cars)): ?>
        <div class="col-12 text-center py-5">
          <div class="mb-4 text-muted" style="opacity: 0.2;">
            <i class="fas fa-car fa-5x"></i>
          </div>
          <h3 class="text-muted font-weight-light">No cars found in this range.</h3>
          <p class="text-muted">Try increasing your search distance.</p>
        </div>
      <?php else: ?>
        <?php foreach ($cars as $car): ?>
          <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="car-card">
              <div class="car-img-wrapper">
                <?php
                $img_src = !empty($car['r_car']) ? 'images/' . $car['r_car'] : 'images/default-car.jpg';
                // Basic fallback logic
                if (!file_exists($img_src) && !empty($car['r_car'])) {
                  // Assume absolute path or external if not found? 
                  // For now left as is, matching previous logic
                }
                ?>
                <img src="<?php echo $img_src; ?>" alt="<?php echo e($car['r_mname']); ?>"
                  onerror="this.src='images/default-car.jpg'">
                <div class="car-price-badge">₹<?php echo e($car['rent_amt']); ?> <span
                    style="font-weight:400; font-size:0.8em">/day</span></div>
              </div>

              <div class="car-body">
                <h4 class="car-title"><?php echo e($car['r_company']); ?>     <?php echo e($car['r_mname']); ?></h4>
                <div class="car-categories"><?php echo e($car['r_type'] ?? 'Premium'); ?> Class</div>

                <div class="car-specs">
                  <div class="spec-item"><i class="fas fa-calendar-alt"></i> <?php echo e($car['r_year']); ?> Model</div>
                  <div class="spec-item"><i class="fas fa-users"></i> <?php echo e($car['r_seat']); ?> Seats</div>
                  <div class="spec-item"><i class="fas fa-gas-pump"></i> ₹<?php echo e($car['r_ppkm']); ?>/km</div>
                  <div class="spec-item"><i class="fas fa-cogs"></i> Manual</div>
                </div>

                <div class="star-rating">
                  <?php
                  $rating_query = "SELECT AVG(rating) as avg_rate, COUNT(*) as count FROM rating WHERE l_id = ?";
                  $rate_data = db_fetch_one($con, $rating_query, "i", [$car['rl_id']]);
                  $avg_rate = $rate_data ? round($rate_data['avg_rate'] ?? 0) : 0;
                  $count = $rate_data ? ($rate_data['count'] ?? 0) : 0;

                  if ($count > 0) {
                    for ($i = 1; $i <= 5; $i++) {
                      echo '<i class="' . ($i <= $avg_rate ? 'fas' : 'far') . ' fa-star"></i>';
                    }
                    echo ' <span class="text-muted ml-1 small">(' . $count . ')</span>';
                  } else {
                    echo '<span class="text-muted small"><i class="far fa-star"></i> No ratings yet</span>';
                  }
                  ?>
                </div>

                <div class="row no-gutters mt-3">
                  <div class="col-8 pr-2">
                    <a href="viewcarprofile.php?r_id=<?php echo e($car['r_id']); ?>"
                      class="btn btn-primary btn-block rounded-lg font-weight-bold shadow-sm"
                      style="border-radius: 10px; padding: 10px;">
                      <i class="fas fa-key mr-2"></i> Book Now
                    </a>
                  </div>
                  <div class="col-4">
                    <a href="viewreviews.php?r_id=<?php echo e($car['rl_id']); ?>"
                      class="btn btn-outline-secondary btn-block rounded-lg" style="border-radius: 10px; padding: 10px;"
                      title="Reviews">
                      <i class="fas fa-comment-alt"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php include 'templates/footer.php'; ?>