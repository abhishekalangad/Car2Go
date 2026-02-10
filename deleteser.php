<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_role('admin');

if (isset($_GET['sl_id'])) {
	$id = (int) $_GET['sl_id'];

	// Delete related records first if cascading isn't set up
	// Assuming simple deletion for now as in legacy
	$delete_service = "DELETE FROM service_reg WHERE sl_id = ?";
	if (db_execute($con, $delete_service, "i", [$id])) {
		$delete_login = "DELETE FROM login WHERE l_id = ?";
		db_execute($con, $delete_login, "i", [$id]);
		redirect_with_message('viewservice.php', 'Service center deleted successfully.', 'success');
	} else {
		redirect_with_message('viewservice.php', 'Failed to delete service center.', 'danger');
	}
} else {
	redirect_with_message('viewservice.php', 'Invalid request.', 'danger');
}
?>