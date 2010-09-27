<?

//-------------------------------------------------------------------------------------------------
//	display an item from the sync queue
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	auth and reference
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if ('' == $req->ref) { $page->do404(); }

	//---------------------------------------------------------------------------------------------
	//	render the page
	//---------------------------------------------------------------------------------------------
	$page->load('modules/sync/actions/showqueueitem.page.php');
	$page->blockArgs['itemUID'] = $req->ref;
	$page->render();

?>
