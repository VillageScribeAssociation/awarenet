<?

//--------------------------------------------------------------------------------------------------------------
//	page for manging modules
//--------------------------------------------------------------------------------------------------------------

	if (file_exists($installPath . 'modules/' . $req->ref . '/module.xml.php') == false) {
		$_SESSION['sMessage'] .= "Module " . $req->ref . " not found.<br/>\n";
		$page->do302('modules/');
	}

	$page->load('modules/mods/actions/manage.page.php');
	$page->blockArgs['modulename'] = $req->ref;
	$page->render();

?>
