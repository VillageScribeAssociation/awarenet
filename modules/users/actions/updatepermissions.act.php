<?

//--------------------------------------------------------------------------------------------------
//	update permissions in user records from module.xml.php files
//--------------------------------------------------------------------------------------------------

	//if ($user->data['ofGroup'] != 'admin') { do403(); }

	authUpdatePermissions();
	$_SESSION['sMessage'] .= "User permissions updated from module configuration files.<br/>\n";
	do302('admin/');

?>
