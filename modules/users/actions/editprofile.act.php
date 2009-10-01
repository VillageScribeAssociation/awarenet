<?

//--------------------------------------------------------------------------------------------------
//	users may edit their own profiles, and admins can edit anything
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { do404(); }
	raFindRedirect('users', 'editprofile', 'users', $request['ref']);
	
	$authorised = false;
	$userUID = raGetOwner($request['ref'], 'users');
	if ($userUID = $user->data['UID']) { $authorised = true; }
	if ($user->data['ofGroup'] == 'admin') { $authorised = true; }
	if ($authorised == false) { do403(); } 

	$page->load($installPath . 'modules/users/actions/editprofile.page.php');
	$page->blockArgs['userRa'] = $request['ref'];
	$page->blockArgs['userUID'] = raGetOwner($request['ref'], 'users');
	$page->blockArgs['userName'] = $user->getName();	// will be wrong for other peoples profile
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['UID'] = raGetOwner($request['ref'], 'users');
	$page->render();

?>
