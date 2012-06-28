<?

	require_once($kapenta->installPath . 'modules/chat/inc/io.class.php');
	require_once($kapenta->installPath . 'modules/chat/models/sessions.set.php');
	require_once($kapenta->installPath . 'modules/chat/models/session.mod.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a user logs out, or their session expires
//--------------------------------------------------------------------------------------------------
//TODO: consider adding 'sessionUID' to chat_session, route message to multiple sessions

function chat__cb_users_logout($args) {
	global $theme;
	global $db;
	global $session;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return false; }

	$io = new Chat_IO();		//TODO: find a more efficient way to do this

	//----------------------------------------------------------------------------------------------
	//	recalculate local users hash
	//----------------------------------------------------------------------------------------------
	//$hashes = new Chat_Hashes();
	//$hashes->sl();
	//TODO: re-implement this using Users_Sessions collection object

	//----------------------------------------------------------------------------------------------
	//	remove chat session
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "serverUID='" . $db->addMarkup($io->myUID) . "'";
	$conditions[] = "userUID='" . $db->addMarkup($args['userUID']) . "'";
	$range = $db->loadRange('chat_session', '*', $conditions);
	foreach($range as $item) {
		$model = new Chat_Session($item['UID']);
		$check = $model->delete();		
		if (true == $check) { $session->msg("Ended chat session."); }
		else { $session->msg("Could not end chat session: " . $report, 'bad'); }
	}

}

?>
