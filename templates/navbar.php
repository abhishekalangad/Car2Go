<?php
/**
 * Navigation Bar Template
 * Displays different navigation based on user type
 */

// Get current user info
$is_logged_in = is_logged_in();
$user_type = $_SESSION['l_type'] ?? '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $base_url ?? '/'; ?>">
            <i class="fas fa-car"></i> CAR2GO
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if ($is_logged_in): ?>
                <!-- Logged In Navigation -->
                <ul class="navbar-nav mr-auto">
                    <?php if ($user_type === 'admin'): ?>
                        <!-- Admin Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/cars.php">
                                <i class="fas fa-car"></i> Manage Cars
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/drivers.php">
                                <i class="fas fa-user-tie"></i> Manage Drivers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/services.php">
                                <i class="fas fa-tools"></i> Manage Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/users.php">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </li>

                    <?php elseif ($user_type === 'user'): ?>
                        <!-- User Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="/user/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/viewcars.php">
                                <i class="fas fa-car"></i> Find Cars
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/viewdriv.php">
                                <i class="fas fa-user-tie"></i> Find Drivers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/viewservicee1.php">
                                <i class="fas fa-tools"></i> Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/rentingform.php">
                                <i class="fas fa-plus-circle"></i> List My Car
                            </a>
                        </li>

                    <?php elseif ($user_type === 'driver'): ?>
                        <!-- Driver Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="/driver/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/ubookdriver.php">
                                <i class="fas fa-calendar-check"></i> My Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/dvfeed.php">
                                <i class="fas fa-star"></i> My Ratings
                            </a>
                        </li>

                    <?php elseif ($user_type === 'service center'): ?>
                        <!-- Service Center Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="/service/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/serviceform.php">
                                <i class="fas fa-plus-circle"></i> Add Service
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/service/requests.php">
                                <i class="fas fa-clipboard-list"></i> Requests
                            </a>
                        </li>

                    <?php endif; ?>
                </ul>

                <!-- User Menu -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                            <i class="fas fa-user-circle"></i>
                            <?php echo e($_SESSION['l_email'] ?? 'Account'); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="/profile.php">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a class="dropdown-item" href="/settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>

            <?php else: ?>
                <!-- Public Navigation -->
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about.php">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact.php">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="registerDropdown" data-toggle="dropdown">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="/userreg.php">
                                <i class="fas fa-user"></i> As User
                            </a>
                            <a class="dropdown-item" href="/driverreg.php">
                                <i class="fas fa-user-tie"></i> As Driver
                            </a>
                            <a class="dropdown-item" href="/servicereg.php">
                                <i class="fas fa-tools"></i> As Service Center
                            </a>
                        </div>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>