<?

//--------------------------------------------------------------------------------------------------
//	display the current user's sent items
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public user cannot check mail
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }

	$pageNo = 1;
	if (array_key_exists('page', $req->args) == true) { $pageNo = $req->args['page']; }

	$page->load('modules/messages/actions/outbox.page.php');
	$page->blockArgs['raUID'] = $user->alias;
	$page->blockArgs['UID'] = $user->UID;
	$page->blockArgs['pageno'] = $pageNo;
	$page->blockArgs['userName'] = $user->getName();
	$page->blockArgs['userRa'] = $user->alias;
	$page->render();	

?>
