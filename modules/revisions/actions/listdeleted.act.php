<?

//--------------------------------------------------------------------------------------------------
//*	list deleted objects
//--------------------------------------------------------------------------------------------------

	$pageNo = 1;
	$objectType = '*';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role (only administrators may do this)
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = (int)$kapenta->request->args['page']; }
	if (true == array_key_exists('type', $kapenta->request->args)) { $objectType = $kapenta->request->args['type']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/revisions/actions/listdeleted.page.php');
	$kapenta->page->blockArgs['pageNo'] = $pageNo;
	$kapenta->page->blockArgs['objectType'] = $objectType;
	$kapenta->page->render();

?>
