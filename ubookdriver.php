<?php
// Redirector for legacy routes
session_start();
if (isset($_SESSION['l_type']) && $_SESSION['l_type'] === 'driver') {
   header("Location: driver/assignments.php");
} else {
   header("Location: user/bookings.php");
}
exit();