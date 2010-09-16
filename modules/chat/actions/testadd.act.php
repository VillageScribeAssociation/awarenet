<?

//--------------------------------------------------------------------------------------------------
//	add a test message to an abritrary queue 
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	if ( (array_key_exists('action', $_POST) == true) 
		AND ($_POST['action'] == 'addTestMessage') ) {

		require_once($kapenta->installPath . 'modules/chat/models/chat.mod.php');

		$chat = new Chat($user->UID);
		$chat->addMessage($_POST['from'], $_POST['to'], $_POST['message']);

		$page->do302('chat/test/');

	} else { echo 'error, incorrect action.'; }

?>
