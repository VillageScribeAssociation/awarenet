<?

	require_once($kapenta->installPath . 'modules/chat/inc/io.class.php');
	require_once($kapenta->installPath . 'modules/chat/models/hashes.set.php');
	require_once($kapenta->installPath . 'modules/chat/models/session.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/sessions.set.php');
	require_once($kapenta->installPath . 'modules/chat/inc/client.class.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a user logs in
//--------------------------------------------------------------------------------------------------
//+	Create a new local Chat_Session object (should be automatically sent to server on next poll)
//TODO: consider adding 'sessionUID' to chat_session, route message to multiple sessions

function chat__cb_users_login($args) {
	global $theme;
	global $session;
	global $db;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return false; }

	$io = new Chat_IO();			// ineffiecient, consider replacing with block or something 

	//----------------------------------------------------------------------------------------------
	//	delete any existing chat session for this user
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "serverUID='" . $db->addMarkup($io->myUID) . "'";
	$conditions[] = "userUID='" . $db->addMarkup($args['userUID']) . "'";
	$range = $db->loadRange('chat_session', '*', $conditions);

	foreach($range as $item) {
		$model = new Chat_Session($item['UID']);
		$check = $model->delete();
		if (true == $check) { $session->msg('Removing previous chat session ' . $item['UID']); }
		else { $session->msg('could not remove previous chat session ' . $item['UID'] . '.'); }
	}

	//----------------------------------------------------------------------------------------------
	//	create new local chat session
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Session();
	$model->status = 'local';
	$model->serverUID = $io->myUID;
	$model->userUID = $args['userUID'];
	$model->shared = 'no';
	$report = $model->save();
	if ('' == $report) { $session->msg('Created new local chat session.', 'ok'); }
	else { $session->msg('Could not create chat session: ' . $report, 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	recalculate local users hash
	//----------------------------------------------------------------------------------------------
	//$hashes = new Chat_Hashes();
	//$hashes->sl();
	//TODO: this

	//----------------------------------------------------------------------------------------------
	//	notify chat server of the logout
	//----------------------------------------------------------------------------------------------
	//removed: making this syncronous / blocking could prevent people logging in to awareNet
	//$client = new Chat_Client();
	//$client->check();

}

?>
