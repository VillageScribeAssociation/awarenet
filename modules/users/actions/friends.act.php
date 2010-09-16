<?

//--------------------------------------------------------------------------------------------------
//*	display a users friends ( and their grade?)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ($user->role == 'public') { $page->do403(); }	

	//TODO: finer controls and permissions for profile view (only friends classmates, etc)

	$model = new Users_user();
	if ('' != $req->ref) { $model->load($req->ref); }	// if a user was specified, try load it
	else { $model->loadArray($user->toArray()); }		// if not, default to current user

	if (false == $model->loaded) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$userName = $model->getName();
	$page->load('modules/users/actions/friends.page.php');
	$page->blockArgs['userUID'] = $model->UID;
	$page->blockArgs['userRa'] = $req->ref;
	$page->blockArgs['userName'] = $userName;
	$page->title = 'awareNet - friends of ' . $userName;
	$page->render();

?>
