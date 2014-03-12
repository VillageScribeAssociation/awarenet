<?

//--------------------------------------------------------------------------------------------------
//*	action to show 'my account' page - does not accept reference
//--------------------------------------------------------------------------------------------------
//+	NOTE: 'public' user has no My Account page (redirect to signup?) but users in 'public' GROUP
//+	do have accounts and hence My Account pages.

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ($kapenta->user->UID == 'public') { $kapenta->page->do404(); }	// not for public user

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/myaccount.page.php');
	$kapenta->page->blockArgs['UID'] = $kapenta->user->UID;
	$kapenta->page->blockArgs['userUID'] = $kapenta->user->UID;
	$kapenta->page->blockArgs['userRA'] = $kapenta->user->alias;
	$kapenta->page->blockArgs['userRa'] = $kapenta->user->alias;
	$kapenta->page->blockArgs['userName'] = $kapenta->user->getName();
	$kapenta->page->render();

?>
