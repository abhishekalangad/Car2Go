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

// Initialize filters
$km = (int) ($_POST['km'] ?? $_GET['km'] ?? 100000);
$name = sanitize_input($_POST['name'] ?? $_GET['name'] ?? '');
$service_type = sanitize_input($_POST['service_type'] ?? $_GET['service_type'] ?? '');

$query = "SELECT DISTINCT s.* FROM service_reg s";
$params = [];
$types = "";

if (!empty($service_type)) {
  $query .= " JOIN service_details sd ON s.sl_id = sd.sel_id WHERE sd.se_name LIKE ?";
  $params[] = "%$service_type%";
  $types .= "s";
} else {
  $query .= " WHERE 1=1";
}

// Distance filter
if ($km < 100000) {
  $hpin = $u_pincode - $km;
  $lpin = $u_pincode + $km;
  $query .= " AND (s.s_pincode BETWEEN ? AND ?)";
  $params[] = $hpin;
  $params[] = $lpin;
  $types .= "ii";
}

// Name filter
if (!empty($name)) {
  $query .= " AND s.s_name LIKE ?";
  $params[] = "%$name%";
  $types .= "s";
}

if (isset($_POST['search']) || isset($_GET['filter'])) {
  $service_centers = db_fetch_all($con, $query, $types, $params);
  $search_performed = true;
} else {
  // Show all centers by default, limited to 12
  $service_centers = db_fetch_all($con, $query . " LIMIT 12", $types, $params);
}

$page_title = 'Find Service Centers - CAR2GO';
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
    background: url('images/bg8.jpg') center/cover;
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

  /* Service Card Design */
  .service-card {
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

  .service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: #3b82f6;
  }

  .card-img-top {
    height: 180px;
    width: 100%;
    object-fit: cover;
    background: #f1f5f9;
    position: relative;
  }

  .status-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(34, 197, 94, 0.9);
    color: white;
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(4px);
  }

  .card-body {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }

  .card-title {
    font-weight: 700;
    font-size: 1.15rem;
    margin-bottom: 0.5rem;
    color: #1e293b;
  }

  .card-meta {
    font-size: 0.9rem;
    color: #64748b;
    margin-bottom: 1rem;
    flex-grow: 1;
  }

  .card-meta div {
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
  }

  .card-meta i {
    width: 20px;
    text-align: center;
    margin-right: 8px;
    color: #3b82f6;
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

  /* Helpers for BS3 */
  .d-flex {
    display: flex;
  }

  .align-items-center {
    align-items: center;
  }

  .justify-content-between {
    justify-content: space-between;
  }

  .mb-0 {
    margin-bottom: 0 !important;
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

    .service-card {
      margin-bottom: 2rem;
    }

    .card-img-top {
      height: 160px;
    }

    .card-title {
      font-size: 1.1rem;
    }
  }
</style>

<div class="main-content">

  <!-- Hero Section -->
  <div class="page-hero">
    <div class="container hero-content">
      <h1 class="font-weight-bold mb-2 animate__animated animate__fadeInDown">Expert Vehicle Care</h1>
      <p class="lead text-white-50 animate__animated animate__fadeInUp">Find certified, top-rated service centers near
        you</p>
    </div>
  </div>

  <!-- Floating Search Section -->
  <div class="container search-card-container animate__animated animate__fadeInUp animate__delay-1s">
    <div class="glass-search">
      <form action="" method="POST">
        <div class="row">
          <!-- Radius -->
          <div class="col-lg-4 col-md-6 mb-3">
            <label class="text-uppercase text-muted font-weight-bold small">Search Radius</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent border-right-0"><i
                    class="fas fa-map-marked-alt text-primary"></i></span>
              </div>
              <select name="km" class="form-control form-control-lg border-left-0">
                <option value="10" <?php echo $km == 10 ? 'selected' : ''; ?>>Within 10 km</option>
                <option value="20" <?php echo $km == 20 ? 'selected' : ''; ?>>Within 20 km</option>
                <option value="50" <?php echo $km == 50 ? 'selected' : ''; ?>>Within 50 km</option>
                <option value="100000" <?php echo $km == 100000 ? 'selected' : ''; ?>>All Locations</option>
              </select>
            </div>
          </div>

          <!-- Service Type -->
          <div class="col-lg-3 col-md-6 mb-3">
            <label class="text-uppercase text-muted font-weight-bold small">Service Needed</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent border-right-0"><i
                    class="fas fa-tools text-primary"></i></span>
              </div>
              <input type="text" name="service_type" class="form-control form-control-lg border-left-0"
                placeholder="e.g. Wash, Paint..." value="<?php echo e($service_type); ?>">
            </div>
          </div>

          <!-- Center Name -->
          <div class="col-lg-3 col-md-6 mb-3">
            <label class="text-uppercase text-muted font-weight-bold small">Center Name</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent border-right-0"><i
                    class="fas fa-building text-primary"></i></span>
              </div>
              <input type="text" name="name" class="form-control form-control-lg border-left-0"
                placeholder="Search by name..." value="<?php echo e($name); ?>">
            </div>
          </div>

          <div class="col-lg-2 col-md-6">
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
        <?php echo $search_performed ? 'Search Results' : 'Recommended Centers'; ?>
      </h3>
      <span class="badge badge-light p-2" style="font-size: 0.9rem; color: #64748b;">
        <?php echo count($service_centers); ?> centers found
      </span>
    </div>

    <div class="row">
      <?php if (empty($service_centers)): ?>
        <div class="col-12 text-center py-5">
          <div class="mb-4 text-muted" style="opacity: 0.2;">
            <i class="fas fa-tools fa-5x"></i>
          </div>
          <h3 class="text-muted font-weight-light">No service centers found nearby.</h3>
          <p class="text-muted">Try increasing the search radius or check back later.</p>
        </div>
      <?php else: ?>
        <?php foreach ($service_centers as $sc): ?>
          <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="service-card">
              <div class="card-img-top">
                <?php
                $img_src = !empty($sc['s_rc']) ? 'uploads/services/' . $sc['s_rc'] : 'images/default-service.jpg';
                // Fallback if file doesn't exist
                if (!file_exists($img_src) && !empty($sc['s_rc'])) {
                  // check absolute path if needed, or just assume relative
                }
                ?>
                <img src="<?php echo $img_src; ?>" alt="<?php echo e($sc['s_name']); ?>"
                  style="width:100%; height:100%; object-fit:cover;" onerror="this.src='images/default-service.jpg'">
                <span class="status-badge"><i class="fas fa-check-circle mr-1"></i> Verified</span>
              </div>
              <div class="card-body">
                <h4 class="card-title"><?php echo e($sc['s_name']); ?></h4>
                <div class="card-meta">
                  <div><i class="fas fa-map-marker-alt"></i> <?php echo e($sc['s_address']); ?></div>
                  <div><i class="fas fa-phone-alt"></i> <?php echo e($sc['s_phone']); ?></div>
                  <div><i class="fas fa-map-pin"></i> Pin Code: <?php echo e($sc['s_pincode']); ?></div>
                </div>
                <div class="mt-3">
                  <a href="viewserviceprofile.php?sl_id=<?php echo $sc['sl_id']; ?>"
                    class="btn btn-outline-primary btn-block rounded-pill font-weight-bold">
                    View Details
                  </a>
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