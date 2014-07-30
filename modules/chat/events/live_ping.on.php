<?

	require_once($kapenta->installPath . 'modules/chat/inc/io.class.php');
	require_once($kapenta->installPath . 'modules/chat/models/session.mod.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a page pings awareNet looking for updates
//--------------------------------------------------------------------------------------------------

function chat__cb_live_ping($args) {
	global $db;
	global $session;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments and relevance
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('user', $args)) { return false; }
	if (false == array_key_exists('role', $args)) { return false; }
	if ('public' == $args['role']) { return false; }

	//----------------------------------------------------------------------------------------------
	//	try load this user's chat session
	//----------------------------------------------------------------------------------------------
	$io = new Chat_IO();				//TODO: inefficient, improve
	$model = new Chat_Session();
	$conditions = array();
	$conditions[] = "serverUID='" . $db->addMarkup($io->myUID) . "'";
	$conditions[] = "userUID='" . $db->addMarkup($args['user']) . "'";

	$range = $db->loadRange('chat_session', '*', $conditions);
	foreach($range as $item) { $model->loadArray($item); }			// there can be only one!

	//----------------------------------------------------------------------------------------------
	//	try create user chat session if one does not already exist
	//----------------------------------------------------------------------------------------------
	if (false == $model->loaded) {
		$model->status = 'local';
		$model->serverUID = $io->myUID;
		$model->userUID = $args['user'];
		$model->shared = 'no';
		$report = $model->save();

		if ('' == $report) { $session->msg("Started new chat session for: ". $args['user'], 'ok'); }
		else { $session->msg("Could not create new chat session: " . $report, 'bad'); }
	}

	//----------------------------------------------------------------------------------------------
	//	update any chat sessions for this user which are more than one(?) minutes old
	//----------------------------------------------------------------------------------------------
	//TODO: registry setting to get rid of this magic number for time
	$editTime = $kapenta->strtotime($model->editedOn);
	$now = $kapenta->time();
	if (($now - $editTime) > 60) {
		$report = $model->save();
		if ('' == $report) {
			/* $session->msg("Updated chat session $now " . $model->userUID, 'ok'); */
		} else { $session->msg("Could not update chat session at $now: " . $report, 'bad'); }
	}

}

?>
