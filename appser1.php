<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_role('admin');

if (isset($_GET['sl_id'])) {
  $id = (int) $_GET['sl_id'];
  $approve = 'approve';

  $query = "UPDATE login SET l_approve = ? WHERE l_id = ?";
  if (db_execute($con, $query, "si", [$approve, $id])) {
    redirect_with_message('viewdriver.php', 'Driver approved successfully.', 'success');
  } else {
    redirect_with_message('viewdriver.php', 'Failed to approve driver.', 'danger');
  }
} else {
  redirect_with_message('viewdriver.php', 'Invalid request.', 'danger');
}
?>