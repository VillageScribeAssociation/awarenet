<?

//--------------------------------------------------------------------------------------------------
//	action for viewing/editing module settings
//--------------------------------------------------------------------------------------------------
	
	if ($user->data['ofGroup'] != admin) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	check that the module is known to the system (protect against directory traversal, etc)
	//----------------------------------------------------------------------------------------------
	$checkRef = false;
	$modList = listModules();
	foreach($modList as $module) { 
		if (strtolower($module) == strtolower($request['ref'])) { $checkRef = true; }
	}

	//----------------------------------------------------------------------------------------------
	//	show the page (or bounce to /)
	//----------------------------------------------------------------------------------------------
	if ($checkRef == true) {
		$page->load($installPath . 'modules/admin/actions/settings.page.php');
		$page->blockArgs['showModule'] = $request['ref'];
		$page->render();
	} else {
		$_SESSION['sMessage'] .= "Invalid module name.<br/>\n";
		do302('/');
	}

?>
