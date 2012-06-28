<?

	require_once($kapenta->installPath . 'modules/chat/models/memberships.set.php');

//--------------------------------------------------------------------------------------------------
//|	make a list of members of a given chat room
//--------------------------------------------------------------------------------------------------
//arg: roomUID - UID of a Chat_Room object [string]

function chat_listmembers($args) {
	global $db;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('roomUID', $args)) { return '(room UID not given)'; }
	$set = new Chat_Memberships($args['roomUID'], true);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$userNames = array();

	foreach($set->members as $item) {
		$userNames[] = "[[:users::namelink::userUID=" . $item['user'] . "::target=_parent:]]";
	}

	$html = implode(", ", $userNames);
	return $html;
}

?>
