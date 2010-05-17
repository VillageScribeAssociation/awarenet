<?

//--------------------------------------------------------------------------------------------------
//	display a users profile
//--------------------------------------------------------------------------------------------------

	if (authHas('users', 'viewprofile', '') == false) { do403(''); }

	//----------------------------------------------------------------------------------------------
	//	which profile to show?
	//----------------------------------------------------------------------------------------------

	// if no user specified then show own profile
	$userRef = $user->data['recordAlias'];
	$userUID = $user->data['UID'];
	$userName = $user->getName();


	// if a specific users profile has been requested
	if ($request['ref'] != '') { 
		raFindRedirect('users', 'profile', 'users', $request['ref']);
		$userRef = $request['ref']; 
		$userUID = raGetOwner($request['ref'], 'users');
		$userName = expandBlocks('[[:users::name::userUID=' . $userUID . ':]]', '');
	}

	//----------------------------------------------------------------------------------------------
	//	user dependant
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/users/actions/profile.page.php');

	if ($userUID == $user->data['UID']) {
		$page->blockArgs['chatButton'] = "";
		$editUrl = "%%serverPath%%/users/editprofile/" . $userRef;
		$editLink = "<a href='" . $editUrl . "'>[change picture]</a><br/>";
		$profilePic = loadBlock('modules/users/views/profilepic.block.php');
		$profilePic = str_replace('%%editLink%%', $editLink, $profilePic);
		$page->blockArgs['profilePicture'] = $profilePic;
	} else {
		$page->blockArgs['chatButton'] = loadBlock('modules/users/views/chatbutton.block.php');
		$profilePic = loadBlock('modules/users/views/profilepic.block.php');
		$profilePic = str_replace('%%editLink%%', '', $profilePic);
		$page->blockArgs['profilePicture'] = $profilePic;
	}


	//----------------c------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------

	$page->blockArgs['userRa'] = $userRef;
	$page->blockArgs['userUID'] = $userUID;
	$page->blockArgs['raUID'] = $userRef;
	$page->blockArgs['UID'] = $userUID;
	$page->blockArgs['userName'] = $userName;
	$page->data['title'] = 'awareNet - ' . $userName . ' (profile)';
	$page->data['jsinit'] .= "msgSubscribe('comments-users-" . $userUID . "', msgh_comments);\n";
	$page->data['jsinit'] .= "msgh_commentsRefresh();\n";
	$page->render();

?>
