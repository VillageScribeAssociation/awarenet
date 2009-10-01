<?

//--------------------------------------------------------------------------------------------------------------
//	action for installing modules
//--------------------------------------------------------------------------------------------------------------
//	Notes: modules should each have an install script called install.inc.php containing a function
//	named install_modulename_module (eg, install_blog_module), which takes no arguments and returns
//	a report.

	if ($user->ofGroup == 'admin') { do403(''); }

	$installMod = $request['ref'];
	
	$incFile = $installPath . 'modules/' . $installMod . '/install.inc.php';
	require_once($incFile);

	echo "incFile: $incFile <br/>\n";
	
	$fnName = 'install_' . $installMod . '_module';
	call_user_func($fnName);

?>
