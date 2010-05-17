<?

//--------------------------------------------------------------------------------------------------
//	edit a wiki article
//--------------------------------------------------------------------------------------------------

	if (authHas('wiki', 'edit', '') == false) { do403(); }			// check permissions
	if ($request['ref'] == '') { do404(); }							// check for ref	
	$UID = raFindRedirect('wiki', 'edit', 'wiki', $request['ref']);	// check correct ref
	
	$page->load($installPath . 'modules/wiki/actions/edit.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $request['ref'];
	$page->render();

?>
