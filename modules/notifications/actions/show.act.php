<?

//--------------------------------------------------------------------------------------------------
//	display the current user's notifications
//--------------------------------------------------------------------------------------------------

	$userUID = $user->data['UID'];

	if ($request['ref'] != '') {
		raFindRedirect('notifications', 'show', 'users', $recordAlias);
		$userUID = raGetOwner('users', $request['ref']);
	}

	$page->load($installPath . 'modules/notifications/actions/show.page.php');
	$page->blockArgs['userUID'] = $userUID;
	$page->blockArgs['userRa'] = $user->data['recordAlias'];
	$page->blockArgs['userName'] = $user->getName();
	$page->render()

?>
