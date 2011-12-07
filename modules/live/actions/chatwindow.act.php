<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	creates a chat iframe
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403('Please log in to use the chat.', true); }	

	if ('' == $req->ref) { $page->do404('User not specified.', true); }
	$model = new Users_User($req->ref);
	if (false == $model->loaded) { $page->do404('User not found.', true); }

	//----------------------------------------------------------------------------------------------
	//	render the page  //TODO: make a generic window template
	//----------------------------------------------------------------------------------------------

	$page->load('modules/live/actions/chatwnd.page.php');
	$page->blockArgs('');
	$page->render();


?>
