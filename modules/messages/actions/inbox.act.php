<?

//--------------------------------------------------------------------------------------------------
//	display the current user's inbox
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public user cannot check mail
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }

	$pageNo = 1;
	if (array_key_exists('page', $req->args) == true) { $pageNo = $req->args['page']; }

	$page->load('modules/messages/actions/inbox.page.php');
	$page->blockArgs['raUID'] = $user->alias;
	$page->blockArgs['UID'] = $user->UID;
	$page->blockArgs['pageno'] = $pageNo;
	$page->blockArgs['userRa'] = $user->alias;
	$page->blockArgs['userName'] = $user->getName();
	$page->render();	

?>
