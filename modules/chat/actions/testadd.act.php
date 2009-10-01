<?

//--------------------------------------------------------------------------------------------------
//	add a test message to an abritrary queue 
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); }

	if ( (array_key_exists('action', $_POST) == true) 
		AND ($_POST['action'] == 'addTestMessage') ) {

		require_once($installPath . 'modules/chat/models/chat.mod.php');

		$chat = new Chat($user->data['UID']);
		$chat->addMessage($_POST['from'], $_POST['to'], $_POST['message']);

		do302('chat/test/');

	} else { echo 'error, incorrect action.'; }

?>
