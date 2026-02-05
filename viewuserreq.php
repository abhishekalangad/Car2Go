<?php
// Redirector for legacy routes
session_start();
header("Location: user/bookings.php");
exit();
