<?

//--------------------------------------------------------------------------------------------------
//*	display a users profile
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	decide which profile to show and check permissions
	//----------------------------------------------------------------------------------------------
	// if no user specified then show own profile
	if ('' == $kapenta->request->ref) { $kapenta->request->ref = $user->alias; }
	$UID = $aliases->findRedirect('users_user');
	$model = new Users_User($UID);
	if (false == $model->loaded) { $kapenta->page->do404('no such user'); }

	$userRef = $model->alias;
	$userUID = $model->UID;
	$userName = $model->getName();

	if (false == $user->authHas('users', 'users_user', 'viewprofile', $UID))
		{ $kapenta->page->do403('you cannot view this profile'); }

	//----------------------------------------------------------------------------------------------
	//	user dependant  //TODO: use $model->extArray() for this
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/profile.page.php');
	$profilePic = $theme->loadBlock('modules/users/views/profilepic.block.php');
	$kapenta->page->blockArgs['chatButton'] = "";

	if ($userUID == $user->UID) {
		$editUrl = "%%serverPath%%/users/editprofile/" . $model->alias;
		$editLink = "<a href='" . $editUrl . "'>[change picture]</a><br/>";
		$profilePic = str_replace('%%editLink%%', $editLink, $profilePic);
		$kapenta->page->blockArgs['profilePicture'] = $profilePic;
	} else {
		$chatBlock = $theme->loadBlock('modules/users/views/chatbutton.block.php');
		$kapenta->page->blockArgs['chatButton'] = $chatBlock;
		$profilePic = str_replace('%%editLink%%', '', $profilePic);
		$kapenta->page->blockArgs['profilePicture'] = $profilePic;
	}

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->blockArgs['userRa'] = $model->alias;
	$kapenta->page->blockArgs['userUID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['userName'] = $userName;
	$kapenta->page->title = 'awareNet - ' . $userName . ' (profile)';

	$kapenta->page->render();

?>
