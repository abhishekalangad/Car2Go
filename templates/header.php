<?php
/**
 * Header Template
 * Common header for all pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include security functions
require_once __DIR__ . '/../includes/security.php';

// Set page title if not already set
if (!isset($page_title)) {
    $page_title = 'CAR2GO - Car Rental & Driver Booking';
}

// Get base URL dynamically if not set
if (!isset($base_url)) {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $project_path = str_replace(basename($script_name), '', $script_name);
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $base_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $project_path;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo e($page_title); ?>
    </title>

    <!-- Meta Tags -->
    <meta name="description" content="CAR2GO - Car rental and driver booking platform">
    <meta name="keywords" content="car rental, driver booking, car service">
    <meta name="author" content="CAR2GO">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>public/images/favicon.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/bootstrap.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">

    <!-- Additional CSS (if any) -->
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $base_url . $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --accent-color: #3b82f6;
            --bg-dark: #0f172a;
            --text-light: #f8fafc;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
        }

        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }


        .navbar {
            padding: 1rem 2rem;
            background:
                <?php echo isset($no_padding) && $no_padding ? 'rgba(15, 23, 42, 0.4)' : 'rgba(15, 23, 42, 0.85)'; ?>
                !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--glass-border);
            transition: all 0.4s ease;
        }

        .navbar-brand {
            font-weight: 800 !important;
            font-size: 1.8rem;
            letter-spacing: -1.5px;
            color: #3b82f6 !important;
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }

        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
        }

        .navbar-dark .navbar-nav .nav-link:hover {
            color: white;
        }

        .dropdown-menu {
            background: var(--bg-dark);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            margin-top: 10px;
        }

        .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.7rem 1.5rem;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .main-content {
            min-height: calc(100vh - 160px);
            padding-top:
                <?php echo isset($no_padding) && $no_padding ? '0' : '76px'; ?>
            ;
            /* Offset for fixed navbar */
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <?php include __DIR__ . '/navbar.php'; ?>

    <!-- Flash Messages -->
    <div class="flash-message">
        <?php echo display_flash_message(); ?>
    </div>

    <!-- Main Content Wrapper -->
    <main class="main-content">