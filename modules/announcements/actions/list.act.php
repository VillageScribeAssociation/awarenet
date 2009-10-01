<?

//--------------------------------------------------------------------------------------------------
//	list all announcements
//--------------------------------------------------------------------------------------------------

	if (authHas('announcements', 'view', '') == false) { do304(); }
	
	$school = $user->data['school'];
	if (array_key_exists('sc', $request['args']) == true) { $school = $request['args']['sc']; }

	$page->load($installPath . 'modules/announcements/actions/list.page.php');
	$page->blockArgs['schoolUID'] = $school;
	$page->render();

?>
