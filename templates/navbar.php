<?php
/**
 * Navigation Bar Template
 * Bootstrap 3.3.4 Compatible Professional Header
 */

// Get current user info
$is_logged_in = is_logged_in();
$user_type = $_SESSION['l_type'] ?? '';

// Fetch Display Name if logged in
$display_name = 'User';
if ($is_logged_in) {
    // Assuming $con is globally available from header.php -> index.php etc.
    global $con;
    if (isset($con)) {
        $display_name = get_user_display_name($con);
    } else {
        $display_name = $_SESSION['l_email'] ?? 'User';
    }
}
?>

<!-- Custom CSS for Modern Look on BS3 -->
<style>
    /* Reset & Overrides */
    #premium-nav {
        position: relative;
        z-index: 1050;
        /* Ensure high stacking order */
    }

    #premium-nav .navbar {
        margin-bottom: 0;
        border: none;
        border-radius: 0;
        min-height: 60px;
        /* Reduced specific height */
    }

    #premium-nav .navbar-inverse {
        background-color: #fff;
        border-bottom: 1px solid #e5e7eb;
    }

    /* Brand */
    #premium-nav .navbar-brand {
        padding: 10px 15px;
        /* Reduced padding */
        height: 60px;
        /* Match new height */
        line-height: 40px;
        font-size: 24px;
        font-weight: 800;
        color: #1a202c !important;
        display: flex;
        align-items: center;
        text-shadow: none !important;
        background: transparent !important;
    }

    #premium-nav .brand-icon {
        color: #2563eb;
        font-size: 0.9em;
        margin-right: 10px;
        display: inline-block;
    }

    /* Links */
    #premium-nav .navbar-nav>li>a {
        padding-top: 20px;
        /* Reduced vertical padding */
        padding-bottom: 20px;
        color: #4a5568 !important;
        font-weight: 600;
        text-transform: none;
        font-size: 14px;
        transition: color 0.2s;
    }

    #premium-nav .navbar-nav>li>a:hover,
    #premium-nav .navbar-nav>li>a:focus,
    #premium-nav .navbar-nav>.open>a {
        color: #2563eb !important;
        background: transparent !important;
    }

    /* Dropdown */
    #premium-nav .dropdown-menu {
        background-color: #ffffff !important;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        padding: 10px 0;
        min-width: 200px;
        margin-top: 0;
        /* Align closer */
    }

    #premium-nav .dropdown-menu>li>a {
        padding: 10px 20px;
        color: #4a5568;
        font-weight: 500;
        font-size: 14px;
    }

    #premium-nav .dropdown-menu>li>a:hover {
        background-color: #f3f4f6;
        color: #2563eb;
    }

    #premium-nav .divider {
        margin: 8px 0;
        background-color: #e5e7eb;
    }

    /* Buttons & Actions */
    #premium-nav .navbar-btn {
        margin-top: 13px;
        /* Adjusted margin */
        margin-bottom: 13px;
    }

    #premium-nav .btn-premium {
        background-color: #2563eb;
        color: white;
        border-radius: 50px;
        padding: 8px 25px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.3s;
        border: none;
    }

    #premium-nav .btn-premium:hover {
        background-color: #1d4ed8;
        color: white;
        transform: translateY(-1px);
    }

    #premium-nav .btn-outline {
        border: 2px solid #e5e7eb;
        color: #4a5568;
        background: transparent;
        border-radius: 50px;
        padding: 6px 20px;
        font-weight: 600;
        font-size: 13px;
        margin-right: 10px;
    }

    #premium-nav .btn-outline:hover {
        border-color: #2563eb;
        color: #2563eb;
    }

    /* Top Bar */
    .top-bar-modern {
        background: #111827;
        color: #9ca3af;
        padding: 6px 0;
        /* Slightly reduced */
        font-size: 12px;
        border-bottom: 1px solid #1f2937;
    }

    .top-bar-modern i {
        margin-right: 6px;
        color: #3b82f6;
    }

    .top-bar-modern a {
        color: #9ca3af;
        transition: color 0.2s;
        text-decoration: none;
    }

    .top-bar-modern a:hover {
        color: white;
    }

    .social-icons a {
        margin-left: 15px;
    }

    /* User Avatar */
    .avatar-circle {
        width: 32px;
        height: 32px;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 50%;
        text-align: center;
        line-height: 32px;
        font-weight: bold;
        display: inline-block;
        margin-right: 8px;
        vertical-align: middle;
        border: 1px solid #dbeafe;
    }

    /* Fix for Mobile Toggle */
    .navbar-toggle .icon-bar {
        background-color: #4a5568 !important;
    }

    /* Overriding Legacy Style.css Conflicts */
    #premium-nav .navbar-default {
        background: #fff !important;
        border: none !important;
    }
</style>

<div id="premium-nav">
    <!-- Top Bar -->
    <div class="top-bar-modern hidden-xs">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <span style="margin-right: 20px;"><i class="fa fa-phone"></i> +1 (234) 567-890</span>
                    <span><i class="fa fa-envelope"></i> support@car2go.com</span>
                </div>
                <div class="col-sm-6 text-right social-icons">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <nav class="navbar navbar-default">
        <div class="container">
            <!-- Brand & Toggle -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo $base_url; ?>index.php">
                    <span class="brand-icon"><i class="fa fa-car"></i></span> CAR2GO
                </a>
            </div>

            <!-- Links -->
            <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                <!-- Logged In Links -->
                <?php if ($is_logged_in): ?>
                    <ul class="nav navbar-nav">
                        <?php if ($user_type === 'admin'): ?>
                            <li><a href="<?php echo $base_url; ?>admin/dashboard.php">Dashboard</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Management <span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $base_url; ?>viewuser.php">Users</a></li>
                                    <li><a href="<?php echo $base_url; ?>viewdriver.php">Drivers</a></li>
                                    <li><a href="<?php echo $base_url; ?>viewservice.php">Service Centers</a></li>
                                    <li><a href="<?php echo $base_url; ?>admin/cars.php">Car Inventory</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">History <span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $base_url; ?>adminviewcar.php">Car Rentals</a></li>
                                    <li><a href="<?php echo $base_url; ?>adminviewdriver.php">Driver Bookings</a></li>
                                    <li><a href="<?php echo $base_url; ?>adminviewservice.php">Service Requests</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Employee <span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $base_url; ?>employereg.php">Register Employee</a></li>
                                    <li><a href="<?php echo $base_url; ?>viewemp.php">View Employees</a></li>
                                </ul>
                            </li>

                        <?php elseif ($user_type === 'user'): ?>
                            <li><a href="<?php echo $base_url; ?>user/dashboard.php">Dashboard</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Browse <span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $base_url; ?>viewcars.php">Rent a Car</a></li>
                                    <li><a href="<?php echo $base_url; ?>viewdriv.php">Hire a Driver</a></li>
                                    <li><a href="<?php echo $base_url; ?>viewservicee1.php">Service Centers</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">My Bookings <span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $base_url; ?>user/bookings.php#cars">Car Rentals</a></li>
                                    <li><a href="<?php echo $base_url; ?>user/bookings.php#drivers">Driver Jobs</a></li>
                                    <li><a href="<?php echo $base_url; ?>user/bookings.php#services">Service History</a></li>
                                </ul>
                            </li>
                            <li><a href="<?php echo $base_url; ?>rentingform.php" style="color: #2563eb !important;"><i
                                        class="fa fa-plus-circle"></i> List Car</a></li>

                        <?php elseif ($user_type === 'driver'): ?>
                            <li><a href="<?php echo $base_url; ?>driver/dashboard.php">Dashboard</a></li>
                            <li><a href="<?php echo $base_url; ?>driver/assignments.php">Assignments</a></li>
                            <li><a href="<?php echo $base_url; ?>dvfeed.php">Ratings</a></li>

                        <?php elseif ($user_type === 'service center'): ?>
                            <li><a href="<?php echo $base_url; ?>service/dashboard.php">Dashboard</a></li>
                            <li><a href="<?php echo $base_url; ?>serviceform.php">New Service</a></li>

                        <?php elseif ($user_type === 'employe'): ?>
                            <li><a href="<?php echo $base_url; ?>employee/dashboard.php">Dashboard</a></li>
                            <li><a href="<?php echo $base_url; ?>viewuser.php">Manage Users</a></li>
                        <?php endif; ?>
                    </ul>

                    <!-- Right Side User Menu -->
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                style="padding-top: 14px; padding-bottom: 14px;">
                                <span class="avatar-circle"><?php echo strtoupper(substr($display_name, 0, 1)); ?></span>
                                <span class="hidden-sm">Account</span>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-header"
                                    style="color: #64748b; font-size: 0.85em; text-transform: uppercase; letter-spacing: 0.5px;">
                                    Signed in as<br>
                                    <strong
                                        style="color: #0f172a; font-size: 1.1em;"><?php echo htmlspecialchars($display_name); ?></strong>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo $base_url; ?>profile.php"><i class="fa fa-user"></i> My Profile</a>
                                </li>
                                <li><a href="<?php echo $base_url; ?>settings.php"><i class="fa fa-cog"></i> Settings</a>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo $base_url; ?>logout.php"><i class="fa fa-sign-out"></i> Logout</a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                <?php else: ?>
                    <!-- Public Links -->
                    <ul class="nav navbar-nav">
                        <li><a href="<?php echo $base_url; ?>index.php">Home</a></li>
                        <li><a href="<?php echo $base_url; ?>about.php">About</a></li>
                        <li><a href="<?php echo $base_url; ?>viewcars.php">Cars</a></li>
                        <li><a href="<?php echo $base_url; ?>contact.php">Contact</a></li>
                    </ul>

                    <!-- Public Actions -->
                    <div class="navbar-right" style="padding-top: 10px; padding-right: 15px;">
                        <a href="<?php echo $base_url; ?>login.php" class="btn btn-outline">Login</a>
                        <a href="<?php echo $base_url; ?>userreg.php" class="btn btn-premium">Join Us</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</div>