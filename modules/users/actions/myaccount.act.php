<?

//--------------------------------------------------------------------------------------------------
//	action to show 'my account' page - does not accept reference
//--------------------------------------------------------------------------------------------------
//	NOTE: 'public' user has no My Account page (redirect to signup?) but users in 'public' GROUP
//	do have accounts and hence My Account pages.

	if ($user->data['UID'] == 'public') { do404(); }	// not for public user

	$page->load($installPath . 'modules/users/actions/myaccount.page.php');
	$page->blockArgs['UID'] = $user->data['UID'];
	$page->blockArgs['userUID'] = $user->data['UID'];
	$page->blockArgs['userRA'] = $user->data['recordAlias'];
	$page->blockArgs['userRa'] = $user->data['recordAlias'];
	$page->blockArgs['userName'] = $user->getName();
	$page->render();

?>
