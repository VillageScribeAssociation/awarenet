<?

//--------------------------------------------------------------------------------------------------
//*	display the current user's notifications
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authorization
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }	// user must be logged in

	$userUID = $user->UID;

	if ('' != $req->ref) {
		if ('admin' == $user->role) {
			// only admins can see other peoples notification feed
			$UID = $aliases->findRedirect('Users_Notification');
			$userUID = $alises->getOwner('users', 'Users_Notification', $req->ref);

		} else { $page->do403(); }
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/notifications/actions/show.page.php');
	$page->blockArgs['userUID'] = $userUID;
	$page->blockArgs['userRa'] = $user->alias;
	$page->blockArgs['userName'] = $user->getName();
	$page->render()

?>
