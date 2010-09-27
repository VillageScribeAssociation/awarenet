<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list of contacts a person messages most often //| TODO: pagination
//--------------------------------------------------------------------------------------------------
//opt: owner - UID of message owner (default is current user) [string]
//opt: num - number of contacts to display (not implemented) [string]

function messages_contactlist($args) {
	global $db, $user;
	$owner = $user->UID;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return false; }
	if (true == array_key_exists('owner', $args)) { $owner = $db->addMarkup($args['owner']); }

	//----------------------------------------------------------------------------------------------
	//	make the contact list
	//----------------------------------------------------------------------------------------------
	$sql = "select count(UID) as numSent, toUID from Messages_Message "
		 . "where owner='" . $owner . "' and fromUID='" . $owner . "' "
		 . "group by toUID order by numSent";
	
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		if ('' != $row['toUID']) {
			$extra = "<a href='/messages/compose/to_" . $row['toUID'] . "'>[send message]</a>";
			$html .= "[[:users::summarynav::userUID=" . $row['toUID'] . "::extra=" . $extra . ":]]";
		}
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
