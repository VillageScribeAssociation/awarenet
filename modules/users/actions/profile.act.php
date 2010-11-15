<?

//--------------------------------------------------------------------------------------------------
//*	display a users profile
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	decide which profile to show and check permissions
	//----------------------------------------------------------------------------------------------
	// if no user specified then show own profile
	if ('' == $req->ref) { $req->ref = $user->alias; }
	$UID = $aliases->findRedirect('Users_User');
	$model = new Users_User($UID);
	if (false == $model->loaded) { $page->do404('no such user'); }

	$userRef = $model->alias;
	$userUID = $model->UID;
	$userName = $model->getName();

	if (false == $user->authHas('users', 'Users_User', 'viewprofile', $UID))
		{ $page->do403('you cannot view this profile'); }

	//----------------------------------------------------------------------------------------------
	//	user dependant  //TODO: use $model->extArray() for this
	//----------------------------------------------------------------------------------------------
	$page->load('modules/users/actions/profile.page.php');
	$profilePic = $theme->loadBlock('modules/users/views/profilepic.block.php');
	$page->blockArgs['chatButton'] = "";

	if ($userUID == $user->UID) {
		$editUrl = "%%serverPath%%/users/editprofile/" . $model->alias;
		$editLink = "<a href='" . $editUrl . "'>[change picture]</a><br/>";
		$profilePic = str_replace('%%editLink%%', $editLink, $profilePic);
		$page->blockArgs['profilePicture'] = $profilePic;
	} else {
		$chatBlock = $theme->loadBlock('modules/users/views/chatbutton.block.php');
		$page->blockArgs['chatButton'] = $chatBlock;
		$profilePic = str_replace('%%editLink%%', '', $profilePic);
		$page->blockArgs['profilePicture'] = $profilePic;
	}


	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$page->blockArgs['userRa'] = $model->alias;
	$page->blockArgs['userUID'] = $model->UID;
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['userName'] = $userName;
	$page->title = 'awareNet - ' . $userName . ' (profile)';
	//$page->jsinit .= "msgSubscribe('comments-users-" . $userUID . "', msgh_comments);\n";
	//$page->jsinit .= "msgh_commentsRefresh();\n";
	$page->render();

?>
