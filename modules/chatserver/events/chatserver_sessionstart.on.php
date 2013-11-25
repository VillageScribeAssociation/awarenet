<?

	require_once($kapenta->installPath . 'modules/chatserver/models/messages.set.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a new global session is created
//--------------------------------------------------------------------------------------------------

function chatserver__cb_chatserver_sessionstart($args) {
	global $db;

	echo "<!-- cb session start -->\n";

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return false; }
	if (false == array_key_exists('peerUID', $args)) { return false; }

	$userUID = $args['userUID'];
	$peerUID = $args['peerUID'];

	if (false == $db->objectExists('user_user', $userUID)) { return false; }
	if (false == $db->objectExists('chatserver_peer', $peerUID)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	re-hint all outgoing chat messages for this user to this peer
	//----------------------------------------------------------------------------------------------
	$messages = new Chatserver_Messages();
	$check = $messages->setPeerForUser($userUID, $peerUID);

	if (true == $check) { echo "<!-- rehinted all messages for $userUID to $peerUID -->\n"; }
	else { echo "<!-- could not rehint $userUID messages to $peerUID -->\n"; }

}


?>
