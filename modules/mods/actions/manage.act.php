<?

//--------------------------------------------------------------------------------------------------------------
//	page for manging modules
//--------------------------------------------------------------------------------------------------------------

	if (file_exists($installPath . 'modules/' . $request['ref'] . '/module.xml.php') == false) {
		$_SESSION['sMessage'] .= "Module " . $request['ref'] . " not found.<br/>\n";
		do302('modules/');
	}

	$page->load($installPath . 'modules/mods/actions/manage.page.php');
	$page->blockArgs['modulename'] = $request['ref'];
	$page->render();

?>
