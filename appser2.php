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
    redirect_with_message('viewuser.php', 'User account approved.', 'success');
  } else {
    redirect_with_message('viewuser.php', 'Failed to approve user.', 'danger');
  }
} else {
  redirect_with_message('viewuser.php', 'Invalid request.', 'danger');
}
?>