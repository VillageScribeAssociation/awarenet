<?

//--------------------------------------------------------------------------------------------------
//	display the current user's notifications
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authorization
	//----------------------------------------------------------------------------------------------
	if ($user->data['ofGroup'] == 'public') { do403(); }	// user must be logged in

	$userUID = $user->data['UID'];

	if ($request['ref'] != '') {
		if ($user->data['ofGroup'] == 'admin') {
			// only admins can see other peoples notification feed
			raFindRedirect('notifications', 'show', 'users', $recordAlias);
			$userUID = raGetOwner('users', $request['ref']);

		} else { do403(); }
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load($installPath . 'modules/notifications/actions/show.page.php');
	$page->blockArgs['userUID'] = $userUID;
	$page->blockArgs['userRa'] = $user->data['recordAlias'];
	$page->blockArgs['userName'] = $user->getName();
	$page->render()

?>
