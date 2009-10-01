<?

//--------------------------------------------------------------------------------------------------
//	view chat history (ones own, admins can view anyones history)
//--------------------------------------------------------------------------------------------------

	$userUID = $user->data['UID'];
	$pageNo = '1';

	if (array_key_exists('page', $request['args'])) { $pageNo = $request['args']['page']; }

	if ( ($rquest['ref'] != '')
		AND (dbRecordExists('users', $request['ref']))
		AND (authHas('chat', 'viewhistory', '') == true) ) { $userUID = $request['ref']; }

	$page->load($installPath . 'modules/chat/actions/history.page.php');
	$page->blockArgs['userUID'] = $userUID;
	$page->render();

?>
