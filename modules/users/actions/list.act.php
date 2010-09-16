<?

//--------------------------------------------------------------------------------------------------
//*	list all users on the system
//--------------------------------------------------------------------------------------------------

	if (false == $user->authHas('users', 'Users_User', 'list')) { $page->do403(''); }
	//TODO: pagination here
	
	$page->load('modules/users/actions/list.page.php');
	$page->render();

?>
