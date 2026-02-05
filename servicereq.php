<?php
// Redirector for legacy routes
session_start();
header("Location: service/tasks.php");
exit();
