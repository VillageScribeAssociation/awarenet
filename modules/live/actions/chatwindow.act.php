<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	creates a chat iframe
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $kapenta->page->do403('Please log in to use the chat.', true); }	

	if ('' == $kapenta->request->ref) { $kapenta->page->do404('User not specified.', true); }
	$model = new Users_User($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('User not found.', true); }

	//----------------------------------------------------------------------------------------------
	//	render the page  //TODO: make a generic window template
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/live/actions/chatwnd.page.php');
	$page->blockArgs('');
	$kapenta->page->render();


?>
