<?

//--------------------------------------------------------------------------------------------------------------
//	edit a billet event
//--------------------------------------------------------------------------------------------------------------

	if (authHas('code', 'edit', '') == false) { do304(); }
	if ($kapenta->request->ref == '') { $kapenta->page->do404(); }
	
	$UID = raFindRedirect('code', 'edit', 'code', $kapenta->request->ref);

	$kapenta->page->load('modules/code/actions/edit.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->render();

?>
