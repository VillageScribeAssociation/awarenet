<?

//--------------------------------------------------------------------------------------------------
//*	page to display a single file
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	
	$kapenta->page->load('modules/files/actions/show.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
