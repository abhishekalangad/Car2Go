<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_role('admin');

if (isset($_GET['sl_id'])) {
	$id = (int) $_GET['sl_id'];

	$delete_driver = "DELETE FROM driver_reg WHERE dl_id = ?";
	if (db_execute($con, $delete_driver, "i", [$id])) {
		$delete_login = "DELETE FROM login WHERE l_id = ?";
		db_execute($con, $delete_login, "i", [$id]);
		redirect_with_message('viewdriver.php', 'Driver deleted successfully.', 'success');
	} else {
		redirect_with_message('viewdriver.php', 'Failed to delete driver.', 'danger');
	}
} else {
	redirect_with_message('viewdriver.php', 'Invalid request.', 'danger');
}
?>