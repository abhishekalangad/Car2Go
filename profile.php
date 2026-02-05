<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_login();

$user_type = $_SESSION['l_type'] ?? '';

switch ($user_type) {
    case 'admin':
        header("Location: admin/dashboard.php");
        break;
    case 'user':
        header("Location: userprofile.php");
        break;
    case 'driver':
        header("Location: driverprofile.php");
        break;
    case 'service center':
        header("Location: serviceprofile.php");
        break;
    default:
        header("Location: index.php");
}
exit();
?>