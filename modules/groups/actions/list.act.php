<?

//--------------------------------------------------------------------------------------------------
//	list all groups in a school (default to user's school)
//--------------------------------------------------------------------------------------------------

	if (authHas('groups', 'show', '') == false) { do403(); }
	
	$school = $user->data['school'];
	if (array_key_exists('sc', $request['args']) == true) { $school = $request['args']['sc']; }

	$page->load($installPath . 'modules/groups/actions/list.page.php');
	$page->blockArgs['schoolUID'] = $school;
	$page->render();

?>
