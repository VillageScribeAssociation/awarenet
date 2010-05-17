<?

//--------------------------------------------------------------------------------------------------
//	page to create a new wiki article
//--------------------------------------------------------------------------------------------------

	if (authHas('wiki', 'edit', '') == false) { do403(); }			// check permissions
	
	$page->load($installPath . 'modules/wiki/actions/new.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $request['ref'];
	$page->render();

?>
