<?

//--------------------------------------------------------------------------------------------------
//*	users may edit their own profiles, and admins can edit anything
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->request->ref = $user->alias; }
	$UID = $aliases->findRedirect('users_user');
	$model = new Users_User($UID);
	if (false == $model->loaded) { $kapenta->page->do404('no such user'); }

	$authorised = false;
	if ($UID == $user->UID) { $authorised = true; }
	if ('admin' == $user->role) { $authorised = true; }
	if (false == $authorised) { $kapenta->page->do403('you cannot edit this profile'); } 

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/editprofile.page.php');
	$kapenta->page->blockArgs['userRa'] = $model->alias;
	$kapenta->page->blockArgs['userUID'] = $model->UID;
	$kapenta->page->blockArgs['userName'] = $model->getName();
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->render();

?>
