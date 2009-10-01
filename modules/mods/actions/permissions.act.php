<?

//--------------------------------------------------------------------------------------------------------------
//	iframe for viewing/editing permissions on a module
//--------------------------------------------------------------------------------------------------------------

	$xmlFile = $installPath . 'modules/' . $request['ref'] . '/module.xml.php';
	if (file_exists($xmlFile) == false) { do404(''); }

	$page->load($installPath . 'modules/mods/actions/permissions.if.page.php');
	$page->blockArgs['modulename'] = $request['ref'];
	$page->render();

?>
