<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Ensure only admin can perform this action
require_role('admin');

if (isset($_GET['sl_id'])) {
	$id = (int) $_GET['sl_id']; // Cast to int for basic sanitization

	// Use prepared statements
	$delete_login = "DELETE FROM login WHERE l_id = ?";
	$delete_emp = "DELETE FROM emp_reg WHERE el_id = ?";

	// Transaction-like approach (though MyISAM might not support it fully, logic holds)
	if (db_execute($con, $delete_emp, "i", [$id])) {
		db_execute($con, $delete_login, "i", [$id]);
		redirect_with_message('viewemp.php', 'Employee deleted successfully.', 'success');
	} else {
		redirect_with_message('viewemp.php', 'Failed to delete employee.', 'danger');
	}
} else {
	redirect_with_message('viewemp.php', 'Invalid request.', 'danger');
}
?>