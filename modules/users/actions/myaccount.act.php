<?

//--------------------------------------------------------------------------------------------------
//*	action to show 'my account' page - does not accept reference
//--------------------------------------------------------------------------------------------------
//+	NOTE: 'public' user has no My Account page (redirect to signup?) but users in 'public' GROUP
//+	do have accounts and hence My Account pages.

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ($user->UID == 'public') { $kapenta->page->do404(); }	// not for public user

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/myaccount.page.php');
	$kapenta->page->blockArgs['UID'] = $user->UID;
	$kapenta->page->blockArgs['userUID'] = $user->UID;
	$kapenta->page->blockArgs['userRA'] = $user->alias;
	$kapenta->page->blockArgs['userRa'] = $user->alias;
	$kapenta->page->blockArgs['userName'] = $user->getName();
	$kapenta->page->render();

?>
