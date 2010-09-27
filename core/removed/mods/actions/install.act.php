<?

//--------------------------------------------------------------------------------------------------------------
//	action for installing modules
//--------------------------------------------------------------------------------------------------------------
//	Notes: modules should each have an install script called install.inc.php containing a function
//	named install_modulename_module (eg, install_blog_module), which takes no arguments and returns
//	a report.

	if ($user->role == 'admin') { $page->do403(''); }

	$installMod = $req->ref;
	
	$incFile = $installPath . 'modules/' . $installMod . '/inc/install.inc.php';
	require_once($incFile);

	echo "incFile: $incFile <br/>\n";
	
	$fnName = 'install_' . $installMod . '_module';
	echo call_user_func($fnName);

?>
