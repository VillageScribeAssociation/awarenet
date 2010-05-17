<?

	require_once($installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list of contacts a person messages most often //| TODO: pagination
//--------------------------------------------------------------------------------------------------
//opt: owner - UID of message owner (default is current user) [string]
//opt: num - number of contacts to display (not implemented) [string]

function messages_contactlist($args) {
	global $user;
	$owner = $user->data['UID']; $html = '';
	if ('public' == $user->data['ofGroup']) { return false; }
	if (array_key_exists('owner', $args) == true) { $owner = sqlMarkup($args['owner']); }

	$sql = "select count(UID) as numSent, toUID from messages "
		 . "where owner='" . $owner . "' and fromUID='" . $owner . "' "
		 . "group by toUID order by numSent";
	
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$extra = "<a href='/messages/compose/to_" . $row['toUID'] . "'>[send message]</a>";
		$html .= "[[:users::summarynav::userUID=" . $row['toUID'] . "::extra=" . $extra . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

