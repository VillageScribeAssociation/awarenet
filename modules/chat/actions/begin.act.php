<?

//--------------------------------------------------------------------------------------------------
//	page to list users to begin chat with, or to start one by adding a blank message to the users
//	queue
//--------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/chat/models/chat.mod.php');

	if ('' != $req->ref) {

		//------------------------------------------------------------------------------------------
		// start the chat
		//------------------------------------------------------------------------------------------

		$toUser = new Users_User($req->ref);

		$queue = new Chat($user->UID);
		$queue->addMessage($user->UID, $toUser->UID, '(***)');

		$queue = new Chat($toUser->UID);
		$queue->addMessage($toUser->UID, $user->UID, '(***)');

	}


	//------------------------------------------------------------------------------------------
	//	list all users
	//------------------------------------------------------------------------------------------

	$page->load('modules/chat/actions/begin.page.php');
	$page->render();

?>
