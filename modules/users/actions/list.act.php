<?

//--------------------------------------------------------------------------------------------------
//*	list all users on the system
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role and arguments
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403('Please log in to view user listings.'); }
	//if (false == $user->authHas('users', 'users_user', 'list')) { $page->do403(''); }

	$pageNo = 1;
	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = (int)$kapenta->request->args['page']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$kapenta->page->load('modules/users/actions/list.page.php');
	$kapenta->page->blockArgs['pageNo'] = $pageNo;
	$kapenta->page->render();

?>
