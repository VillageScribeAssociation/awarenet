<?

	require_once($kapenta->installPath . 'modules/chat/models/sessions.set.php');

//--------------------------------------------------------------------------------------------------
//|	list active sessions for a given peer
//--------------------------------------------------------------------------------------------------
//arg: peerUID - UID of a Chat_Peer object [string]

function chat_listactivesessions($args) {
	global $user;	

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (false == array_key_exists('peerUID', $args)) { return '(peerUID not given)'; }

	//TODO: permissions check here
	//TODO: check peerUID exists

	$set = new Chat_Sessions($args['peerUID'], true);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	
	foreach($set->members as $item) {
		$html .= "[[:users::namelink::userUID=" . $item['userUID'] . ":]]";
		if ('local' == $item['status']) { $html .= "*"; }
		$html .= "<br/>\n";
	}

	return $html;
}


?>
