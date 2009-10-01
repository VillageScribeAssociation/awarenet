<?

//--------------------------------------------------------------------------------------------------
//	display the current user's inbox
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public user cannot check mail
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->data['ofGroup']) { do403(); }

	$pageNo = 1;
	if (array_key_exists('page', $request['args']) == true) { $pageNo = $request['args']['page']; }

	$page->load($installPath . 'modules/messages/actions/inbox.page.php');
	$page->blockArgs['raUID'] = $user->data['recordAlias'];
	$page->blockArgs['UID'] = $user->data['UID'];
	$page->blockArgs['pageno'] = $pageNo;
	$page->blockArgs['userRa'] = $user->data['recordAlias'];
	$page->blockArgs['userName'] = $user->getName();
	$page->render();	

?>
