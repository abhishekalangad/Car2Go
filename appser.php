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
    // Update service_reg status if needed? Legacy only updated login.
    redirect_with_message('viewservice.php', 'Service center approved successfully.', 'success');
  } else {
    redirect_with_message('viewservice.php', 'Failed to approve service center.', 'danger');
  }
} else {
  redirect_with_message('viewservice.php', 'Invalid request.', 'danger');
}
?>