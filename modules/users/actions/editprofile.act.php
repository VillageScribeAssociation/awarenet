<?

//--------------------------------------------------------------------------------------------------
//*	users may edit their own profiles, and admins can edit anything
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $req->ref = $user->alias; }
	$UID = $aliases->findRedirect('users_user');
	$model = new Users_User($UID);
	if (false == $model->loaded) { $page->do404('no such user'); }

	$authorised = false;
	if ($UID == $user->UID) { $authorised = true; }
	if ('admin' == $user->role) { $authorised = true; }
	if (false == $authorised) { $page->do403('you cannot edit this profile'); } 

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/users/actions/editprofile.page.php');
	$page->blockArgs['userRa'] = $model->alias;
	$page->blockArgs['userUID'] = $model->UID;
	$page->blockArgs['userName'] = $model->getName();
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $model->UID;
	$page->render();

?>
