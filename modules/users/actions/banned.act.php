<?

//--------------------------------------------------------------------------------------------------
//*	list all users on the system
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role and arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403('Admins only.'); }
	//if (false == $user->authHas('users', 'users_user', 'list')) { $kapenta->page->do403(''); }

	$pageNo = 1;
	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = (int)$kapenta->request->args['page']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$kapenta->page->load('modules/users/actions/banned.page.php');
	$kapenta->page->blockArgs['pageNo'] = $pageNo;
	$kapenta->page->render();

?>
