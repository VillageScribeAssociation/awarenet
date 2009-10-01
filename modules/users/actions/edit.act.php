<?

//--------------------------------------------------------------------------------------------------
//	edit a user record
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); }


	$page->load($installPath . 'modules/users/actions/edit.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['userUID'] = raGetOwner($request['ref'], 'users');
	$page->render();

?>
