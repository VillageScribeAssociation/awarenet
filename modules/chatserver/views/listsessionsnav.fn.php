<?

	require_once($kapenta->installPath . 'modules/chatserver/models/peers.set.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/sessions.set.php');

//--------------------------------------------------------------------------------------------------
//|	list all global user sessions, formatted for nav
//--------------------------------------------------------------------------------------------------

function chatserver_listsessionsnav($args) {
	global $user;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	$peers = new Chatserver_Peers(true);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	foreach($peers->members as $peer) {
		$html .= "<b>" . $peer['name'] . "</b><br/>\n";
		$sessions = new Chatserver_Sessions($peer['peerUID'], true);

		foreach($sessions->members as $session) {
			$html .= "[[:users::namelink::userUID=" . $session['userUID'] . ":]]<br/>";
		}

	}

	return $html;
}

?>
