<?php
/**
 * Navigation Bar Template
 * Displays different navigation based on user type
 */

// Get current user info
$is_logged_in = is_logged_in();
$user_type = $_SESSION['l_type'] ?? '';
?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
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
                            <a class="nav-link" href="<?php echo $base_url; ?>admin/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>admin/bookings.php">
                                <i class="fas fa-receipt"></i> Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>admin/cars.php">
                                <i class="fas fa-car"></i> Cars
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>admin/drivers.php">
                                <i class="fas fa-user-tie"></i> Drivers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>admin/services.php">
                                <i class="fas fa-tools"></i> Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>admin/users.php">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>

                    <?php elseif ($user_type === 'user'): ?>
                        <!-- User Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>user/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>user/bookings.php">
                                <i class="fas fa-calendar-check"></i> My Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>viewcars.php">
                                <i class="fas fa-car-side"></i> Rent a Car
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>viewdriv.php">
                                <i class="fas fa-user-tie"></i> Find Drivers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>viewservicee1.php">
                                <i class="fas fa-tools"></i> Service Centers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>rentingform.php">
                                <i class="fas fa-plus-circle"></i> List My Car
                            </a>
                        </li>

                    <?php elseif ($user_type === 'driver'): ?>
                        <!-- Driver Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>driver/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>driver/assignments.php">
                                <i class="fas fa-list-ul"></i> Assignments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>dvfeed.php">
                                <i class="fas fa-star"></i> My Ratings
                            </a>
                        </li>

                    <?php elseif ($user_type === 'service center'): ?>
                        <!-- Service Center Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>service/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>service/tasks.php">
                                <i class="fas fa-clipboard-list"></i> Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>serviceform.php">
                                <i class="fas fa-plus-circle"></i> Add Service
                            </a>
                        </li>

                    <?php elseif ($user_type === 'employe'): ?>
                        <!-- Employee Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>employee/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>viewuser.php">
                                <i class="fas fa-users"></i> Manage Users
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
                            <a class="dropdown-item" href="<?php echo $base_url; ?>profile.php">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a class="dropdown-item" href="<?php echo $base_url; ?>settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo $base_url; ?>logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>

            <?php else: ?>
                <!-- Public Navigation -->
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>about.php">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>contact.php">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="registerDropdown" data-toggle="dropdown">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?php echo $base_url; ?>userreg.php">
                                <i class="fas fa-user"></i> As User
                            </a>
                            <a class="dropdown-item" href="<?php echo $base_url; ?>driverreg.php">
                                <i class="fas fa-user-tie"></i> As Driver
                            </a>
                            <a class="dropdown-item" href="<?php echo $base_url; ?>servicereg.php">
                                <i class="fas fa-tools"></i> As Service Center
                            </a>
                        </div>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>