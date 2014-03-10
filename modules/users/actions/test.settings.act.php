<?

//--------------------------------------------------------------------------------------------------
//*	temporary development action to test user settings
//--------------------------------------------------------------------------------------------------
//TODO: once this feature is stable	

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	//$check = $user->set('test.setting', '1234value');
	//if (false == $check) { echo "could not store test setting.<br/>\n"; }

	$check = $user->set('test.another', 'x');
	if (false == $check) { echo "could not store test setting.<br/>\n"; }

	$value = $user->get('test.setting');

	echo "stored and returned: $value <br/>";

	//db select count(UID) as unc, username from users_user group by username order by unc;

?>
