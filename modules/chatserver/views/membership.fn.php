<?

	require_once($kapenta->installPath . 'modules/chatserver/models/memberships.set.php');

//--------------------------------------------------------------------------------------------------
//|	list membership of a chat room
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Chatserver_Room obejct [string]
//arg: roomUID - overrides UID if present [string]

function chatserver_membership($args) {
	global $db;
	global $user;
	global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('roomUID', $args)) { $args['UID'] = $args['roomUID']; }
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }

	$set = new Chatserver_Memberships($args['UID'], true);
	if (false == $set->loaded) { return '(could not load memberships)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	foreach($set->members as $item) {
		$html .= ''
		 . "[[:users::summarynav"
		 . "::userUID=" . $item['user'] 
		 . "::extra=(" . $item['role'] . ")"
		 . ":]]";
	}

	return $html;
}

?>
