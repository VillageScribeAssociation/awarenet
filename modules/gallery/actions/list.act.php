<?

//--------------------------------------------------------------------------------------------------
//*	list all galleries created by a user (in root if nesting)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check basic permissions
	//----------------------------------------------------------------------------------------------

	if (authHas('gallery', 'show', '') == false) { do403(); }	

	//----------------------------------------------------------------------------------------------
	//	decide which users galleries to show
	//----------------------------------------------------------------------------------------------

	$userUID = $user->data['UID'];
	$userName = $user->getName();

	if ($request['ref'] != '') {
		$userUID = raGetOwner($request['ref'], 'users');
		if ($userUID == false) { do404(); }
		$userName = expandBlocks('[[:users::name::userUID=' . $userUID . ':]]', '');
	}

	$userRa = raGetDefault('users', $userUID);

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/gallery/actions/list.page.php');		
	$page->blockArgs['userUID'] = $userUID;								
	$page->blockArgs['userRa'] = $userRa;
	$page->blockArgs['userName'] = $userName;
	$page->data['title'] = 'awareNet - galleries by ' . $userName;
	$page->render();													

?>
