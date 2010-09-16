<?

//--------------------------------------------------------------------------------------------------------------
//	iframe for viewing/editing permissions on a module
//--------------------------------------------------------------------------------------------------------------

	$xmlFile = $installPath . 'modules/' . $req->ref . '/module.xml.php';
	if (file_exists($xmlFile) == false) { $page->do404(''); }

	$page->load('modules/mods/actions/permissions.if.page.php');
	$page->blockArgs['modulename'] = $req->ref;
	$page->render();

?>
