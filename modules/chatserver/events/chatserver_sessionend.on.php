<?

	require_once($kapenta->installPath . 'modules/chatserver/models/messages.set.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a global session ends
//--------------------------------------------------------------------------------------------------

function chatserver__cb_chatserver_sessionend($args) {
	global $db;

	echo "<!-- cb session end -->\n";

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return false; }

	$userUID = $args['userUID'];

	if (false == $db->objectExists('user_user', $userUID)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	clear 'peer' hint all outgoing chat messages for this user
	//----------------------------------------------------------------------------------------------
	$messages = new Chatserver_Messages();
	$check = $messages->setPeerForUser($userUID, '');

	if (true == $check) { echo "<!-- rehinted all messages for $userUID to (unknown) -->\n"; }
	else { echo "<!-- could not rehint $userUID messages to (unknown) -->\n"; }

}

?>
