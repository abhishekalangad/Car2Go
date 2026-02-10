<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_role('admin');

if (isset($_GET['sl_id'])) {
  $id = (int) $_GET['sl_id'];
  $disapprove = 'disapprove';

  $query = "UPDATE login SET l_approve = ? WHERE l_id = ?";
  if (db_execute($con, $query, "si", [$disapprove, $id])) {
    redirect_with_message('viewdriver.php', 'Driver suspended.', 'warning');
  } else {
    redirect_with_message('viewdriver.php', 'Failed to suspend driver.', 'danger');
  }
} else {
  redirect_with_message('viewdriver.php', 'Invalid request.', 'danger');
}
?>