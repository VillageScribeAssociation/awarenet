<?

//--------------------------------------------------------------------------------------------------
//*	list all galleries created by a user (in root if nesting)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check basic permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('gallery', 'Gallery_Gallery', 'show')) { $page->do403(); }	

	//----------------------------------------------------------------------------------------------
	//	decide which users galleries to show
	//----------------------------------------------------------------------------------------------
	$userUID = $user->UID;
	$userName = $user->getName();

	if ('' != $req->ref) {
		$userUID = $aliases->getOwner('Users_User');
		if (false == $userUID) { $page->do404(); }
		$userName = $theme->expandBlocks('[[:users::name::userUID=' . $userUID . ':]]', '');
	}

	$userRa = $aliases->getDefault('users', $userUID);

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/gallery/actions/list.page.php');		
	$page->blockArgs['userUID'] = $userUID;								
	$page->blockArgs['userRa'] = $userRa;
	$page->blockArgs['userName'] = $userName;
	$page->title = 'awareNet - galleries by ' . $userName;
	$page->render();													

?>
