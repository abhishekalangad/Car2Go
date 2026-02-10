<?php
session_start();
// Redirect to Admin Dashboard
if (isset($_SESSION['l_type']) && $_SESSION['l_type'] === 'admin') {
	header("Location: admin/dashboard.php");
} else {
	header("Location: index.php");
}
exit();
?>