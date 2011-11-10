<?

//--------------------------------------------------------------------------------------------------
//*	action to show 'my account' page - does not accept reference
//--------------------------------------------------------------------------------------------------
//+	NOTE: 'public' user has no My Account page (redirect to signup?) but users in 'public' GROUP
//+	do have accounts and hence My Account pages.

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ($user->UID == 'public') { $page->do404(); }	// not for public user

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/users/actions/myaccount.page.php');
	$page->blockArgs['UID'] = $user->UID;
	$page->blockArgs['userUID'] = $user->UID;
	$page->blockArgs['userRA'] = $user->alias;
	$page->blockArgs['userRa'] = $user->alias;
	$page->blockArgs['userName'] = $user->getName();
	$page->render();

?>
