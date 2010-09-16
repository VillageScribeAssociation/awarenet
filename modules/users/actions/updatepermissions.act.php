<?

//--------------------------------------------------------------------------------------------------
//	update permissions in user records from module.xml.php files
//--------------------------------------------------------------------------------------------------

	//if ('admin' != $user->role) { $page->do403(); }

	authUpdatePermissions();
	$_SESSION['sMessage'] .= "User permissions updated from module configuration files.<br/>\n";
	$page->do302('admin/');

?>
