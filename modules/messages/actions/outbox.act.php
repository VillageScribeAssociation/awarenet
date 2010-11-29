<?

//--------------------------------------------------------------------------------------------------
//	display the current user's sent items
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public user cannot check mail
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }


	//----------------------------------------------------------------------------------------------
	//	check for any arguments
	//----------------------------------------------------------------------------------------------
	$pageNo = 1;
	if (true == array_key_exists('page', $req->args)) { $pageNo = $req->args['page']; }

	$orderBy = 'createdOn';
	if (true == array_key_exists('orderBy', $req->args)) {
		$orderBy = $req->args['orderBy'];
		switch ($orderBy) {
			case 'createdOn': 	$orderBy = 'createdOn';			break;
			case 'title': 		$orderBy = 'title';				break;
			case 'toName': 		$orderBy = 'toName';			break;
			case 'status': 		$orderBy = 'status';			break;
			default: 			$orderBy = 'createdOn';			break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/messages/actions/outbox.page.php');
	$page->blockArgs['raUID'] = $user->alias;
	$page->blockArgs['UID'] = $user->UID;
	$page->blockArgs['pageno'] = $pageNo;
	$page->blockArgs['userName'] = $user->getName();
	$page->blockArgs['userRa'] = $user->alias;
	$page->blockArgs['orderBy'] = $orderBy;
	$page->render();	

?>
