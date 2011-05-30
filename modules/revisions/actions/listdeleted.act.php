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
	if (true == array_key_exists('page', $req->args)) { $pageNo = (int)$req->args['page']; }
	if (true == array_key_exists('type', $req->args)) { $objectType = $req->args['type']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/revisions/actions/listdeleted.page.php');
	$page->blockArgs['pageNo'] = $pageNo;
	$page->blockArgs['objectType'] = $objectType;
	$page->render();

?>
