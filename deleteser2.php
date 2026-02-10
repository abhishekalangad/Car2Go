<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

require_role('admin');

if (isset($_GET['sl_id'])) {
	$id = (int) $_GET['sl_id'];

	$delete_user = "DELETE FROM user_reg WHERE ul_id = ?";
	if (db_execute($con, $delete_user, "i", [$id])) {
		$delete_login = "DELETE FROM login WHERE l_id = ?";
		db_execute($con, $delete_login, "i", [$id]);
		redirect_with_message('viewuser.php', 'User account deleted successfully.', 'success');
	} else {
		redirect_with_message('viewuser.php', 'Failed to delete user.', 'danger');
	}
} else {
	redirect_with_message('viewuser.php', 'Invalid request.', 'danger');
}
?>