<?

//--------------------------------------------------------------------------------------------------
//*	show only (and all) notifications made by teachers
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404(); }
	if (('teachers' != $kapenta->request->ref) && ('everyone' != $kapenta->request->ref)) { $page->do404('Unknown feed.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/notifications/actions/by.page.php');
	$kapenta->page->blockArgs['feed'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['userUID'] = $user->UID;
	$kapenta->page->render();

?>
