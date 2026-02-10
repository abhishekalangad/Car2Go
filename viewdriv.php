<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Force login
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
$drivers = [];
$search_performed = false;

// Initialize filters
$km = (int) ($_POST['km'] ?? $_GET['km'] ?? 100000);
$max_fee = (int) ($_POST['max_fee'] ?? $_GET['max_fee'] ?? 10000);
$name = sanitize_input($_POST['name'] ?? $_GET['name'] ?? '');

$sql = "SELECT d.*, l.l_approve, 
        (SELECT AVG(rating) FROM drating WHERE ld_id = d.dl_id) as avg_rating 
        FROM driver_reg d 
        JOIN login l ON d.dl_id = l.l_id 
        WHERE l.l_approve = 'approve'";
$params = [];
$types = "";

// Distance filter
if ($km < 100000) {
   $hpin = $u_pincode - $km;
   $lpin = $u_pincode + $km;
   $sql .= " AND (d.d_pincode BETWEEN ? AND ?)";
   $params[] = $hpin;
   $params[] = $lpin;
   $types .= "ii";
}

// Fee filter
if ($max_fee < 10000) {
   $sql .= " AND d.d_amount <= ?";
   $params[] = $max_fee;
   $types .= "i";
}

// Name filter
if (!empty($name)) {
   $sql .= " AND d.d_name LIKE ?";
   $params[] = "%$name%";
   $types .= "s";
}

if (isset($_POST['search']) || isset($_GET['filter'])) {
   $drivers = db_fetch_all($con, $sql, $types, $params);
   $search_performed = true;
} else {
   $drivers = db_fetch_all($con, $sql . " LIMIT 12", $types, $params);
}

$page_title = 'Professional Drivers - CAR2GO';
include 'templates/header.php';
?>

<style>
   /* Premium Page Styles */
   .page-hero {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      padding: 5rem 0 8rem;
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
      background: url('images/bg7.jpg') center/cover;
      opacity: 0.2;
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

   /* Driver Card Design */
   .driver-card {
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

   .driver-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      border-color: #3b82f6;
   }

   .driver-img-wrapper {
      position: relative;
      height: 240px;
      overflow: hidden;
      background: #f1f5f9;
   }

   .driver-img-wrapper img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
   }

   .driver-card:hover .driver-img-wrapper img {
      transform: scale(1.05);
   }

   .driver-rate-badge {
      position: absolute;
      bottom: 12px;
      left: 12px;
      background: white;
      color: #0f172a;
      padding: 6px 14px;
      border-radius: 50px;
      font-weight: 700;
      font-size: 0.9rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
   }

   .card-body {
      padding: 1.5rem;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      position: relative;
   }

   .verified-badge {
      position: absolute;
      top: -20px;
      right: 20px;
      background: #3b82f6;
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      border: 4px solid white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
   }

   .driver-name {
      font-weight: 800;
      font-size: 1.25rem;
      margin-bottom: 0.25rem;
      color: #1e293b;
   }

   .driver-exp {
      font-size: 0.85rem;
      color: #64748b;
      margin-bottom: 1rem;
      font-weight: 500;
   }

   .driver-stats {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1.5rem;
      background: #f8fafc;
      padding: 12px;
      border-radius: 10px;
   }

   .stat-box {
      text-align: center;
      flex: 1;
      border-right: 1px solid #e2e8f0;
   }

   .stat-box:last-child {
      border-right: none;
   }

   .stat-label {
      font-size: 0.7rem;
      text-transform: uppercase;
      color: #94a3b8;
      letter-spacing: 0.5px;
   }

   .stat-value {
      font-weight: 700;
      color: #334155;
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

      .driver-img-wrapper {
         height: 200px;
      }

      .driver-name {
         font-size: 1.1rem;
      }

      .driver-stats {
         padding: 10px;
      }
   }
</style>

<div class="main-content">

   <!-- Hero Section -->
   <div class="page-hero">
      <div class="container hero-content">
         <h1 class="font-weight-bold mb-2 animate__animated animate__fadeInDown">Elite Chauffeurs</h1>
         <p class="lead text-white-50 animate__animated animate__fadeInUp">Verified professionals for a safe and
            comfortable journey</p>
      </div>
   </div>

   <!-- Floating Search Section -->
   <div class="container search-card-container animate__animated animate__fadeInUp animate__delay-1s">
      <div class="glass-search">
         <form action="viewdriv.php" method="POST">
            <div class="row">
               <!-- Distance -->
               <div class="col-lg-4 col-md-6 mb-3">
                  <label class="text-uppercase text-muted font-weight-bold small">Area Coverage</label>
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

               <!-- Budget -->
               <div class="col-lg-3 col-md-6 mb-3">
                  <label class="text-uppercase text-muted font-weight-bold small">Max Daily Budget</label>
                  <div class="input-group">
                     <div class="input-group-prepend">
                        <span class="input-group-text bg-transparent border-right-0"><i
                              class="fas fa-wallet text-primary"></i></span>
                     </div>
                     <input type="number" name="max_fee" class="form-control form-control-lg border-left-0"
                        placeholder="Up to ₹2,000" value="<?php echo $max_fee < 10000 ? $max_fee : ''; ?>">
                  </div>
               </div>

               <!-- Search by Name -->
               <div class="col-lg-3 col-md-6 mb-3">
                  <label class="text-uppercase text-muted font-weight-bold small">Search Name</label>
                  <div class="input-group">
                     <div class="input-group-prepend">
                        <span class="input-group-text bg-transparent border-right-0"><i
                              class="fas fa-user text-primary"></i></span>
                     </div>
                     <input type="text" name="name" class="form-control form-control-lg border-left-0"
                        placeholder="Driver name..." value="<?php echo e($name); ?>">
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
         <h3 class="font-weight-bold text-dark mb-0">Available Professionals</h3>
         <span class="badge badge-light p-2" style="font-size: 0.9rem; color: #64748b;">
            <?php echo count($drivers); ?> drivers found
         </span>
      </div>

      <div class="row">
         <?php if (empty($drivers)): ?>
            <div class="col-12 text-center py-5">
               <div class="mb-4 text-muted" style="opacity: 0.2;">
                  <i class="fas fa-user-tie fa-5x"></i>
               </div>
               <h3 class="text-muted font-weight-light">No drivers found matching your criteria.</h3>
               <p class="text-muted">Try a simplified search or check back later.</p>
            </div>
         <?php else: ?>
            <?php foreach ($drivers as $driver): ?>
               <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                  <div class="driver-card">
                     <div class="driver-img-wrapper">
                        <?php
                        $img_src = !empty($driver['d_proof']) ? 'uploads/drivers/' . $driver['d_proof'] : 'images/default-user.jpg';
                        // Basic fallback
                        if (!file_exists($img_src) && !empty($driver['d_proof'])) {
                           // Keep as is or add more logic
                        }
                        ?>
                        <img src="<?php echo $img_src; ?>" alt="<?php echo e($driver['d_name']); ?>">
                        <div class="driver-rate-badge">₹<?php echo e($driver['d_amount']); ?>/day</div>
                     </div>

                     <div class="card-body">
                        <div class="verified-badge" title="Verified Driver">
                           <i class="fas fa-check"></i>
                        </div>
                        <h4 class="driver-name"><?php echo e($driver['d_name']); ?></h4>
                        <div class="driver-exp"><i class="fas fa-map-marker-alt text-primary mr-1"></i>
                           <?php echo e($driver['d_address']); ?></div>

                        <div class="driver-stats">
                           <div class="stat-box">
                              <div class="stat-value text-warning">
                                 <?php echo !empty($driver['avg_rating']) ? number_format($driver['avg_rating'], 1) : 'NEW'; ?>
                                 <i class="fas fa-star small"></i>
                              </div>
                              <div class="stat-label">Rating</div>
                           </div>
                           <div class="stat-box">
                              <div class="stat-value">5+</div>
                              <div class="stat-label">Years Exp</div>
                           </div>
                           <div class="stat-box">
                              <div class="stat-value">100%</div>
                              <div class="stat-label">Verified</div>
                           </div>
                        </div>

                        <a href="viewdriverprofile.php?dl_id=<?php echo $driver['dl_id']; ?>"
                           class="btn btn-primary btn-block rounded-pill font-weight-bold shadow-sm" style="padding: 12px;">
                           Hire Driver
                        </a>
                     </div>
                  </div>
               </div>
            <?php endforeach; ?>
         <?php endif; ?>
      </div>
   </div>

</div>

<?php include 'templates/footer.php'; ?>