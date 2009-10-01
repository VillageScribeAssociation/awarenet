<?

//--------------------------------------------------------------------------------------------------
//	page to list users to begin chat with, or to start one by adding a blank message to the users
//	queue
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/chat/models/chat.mod.php');

	if ($request['ref'] != '') {

		//------------------------------------------------------------------------------------------
		// start the chat
		//------------------------------------------------------------------------------------------

		$toUser = new Users($request['ref']);

		$queue = new Chat($user->data['UID']);
		$queue->addMessage($user->data['UID'], $toUser->data['UID'], '(***)');

		$queue = new Chat($toUser->data['UID']);
		$queue->addMessage($toUser->data['UID'], $user->data['UID'], '(***)');

	}


	//------------------------------------------------------------------------------------------
	//	list all users
	//------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/chat/actions/begin.page.php');
	$page->render();

?>
