<?

//--------------------------------------------------------------------------------------------------
//*	display a users friends ( and their grade?)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ($user->role == 'public') { $kapenta->page->do403(); }	

	//TODO: finer controls and permissions for profile view (only friends classmates, etc)

	$model = new Users_user();
	if ('' != $kapenta->request->ref) { $model->load($kapenta->request->ref); }	// if a user was specified, try load it
	else { $model->loadArray($user->toArray()); }		// if not, default to current user

	if (false == $model->loaded) { $kapenta->page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$userName = $model->getName();
	$kapenta->page->load('modules/users/actions/friends.page.php');
	$kapenta->page->blockArgs['userUID'] = $model->UID;
	$kapenta->page->blockArgs['userRa'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['userName'] = $userName;
	$kapenta->page->title = 'awareNet - friends of ' . $userName;
	$kapenta->page->render();

?>
