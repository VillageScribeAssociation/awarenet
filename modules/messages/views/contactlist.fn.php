<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list of contacts a person messages most often //| TODO: pagination
//--------------------------------------------------------------------------------------------------
//opt: owner - UID of message owner (default is current user) [string]
//opt: num - number of contacts to display (not implemented) [string]
//TODO: consider adding a 'contacts' object/index table to avoid the need to the query below.

function messages_contactlist($args) {
		global $kapenta;
		global $user;


	$owner = $user->UID;		//%	user to show contact list for [string]
	$num = 10;					//%	default number of contacts to show [int]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return false; }
	if (true == array_key_exists('owner', $args)) { $owner = $args['owner']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	make the contact list
	//----------------------------------------------------------------------------------------------
	//TODO: cache this list somewhere, figure out a way to avoid this query

	$sql = ''
	 . "SELECT count(UID) as numSent, toUID FROM messages_message"
	 . " WHERE owner='" . $kapenta->db->addMarkup($owner) . "'"
	 . " AND fromUID='" . $kapenta->db->addMarkup($owner) . "'"
	 . " GROUP BY toUID"
	 . " ORDER BY numSent"
	 . " LIMIT $num";
	
	$arrowLeft = '%%serverPath%%themes/%%defaultTheme%%/images/icons/arrow_left_green.png';

	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		if ('' != $row['toUID']) {

			//	PREVIOUS VERSION - may still be useful, for example on profile

			#$extra = "<a href='/messages/compose/to_" . $row['toUID'] . "'>[send message]</a>";
			#$html .= "[[:users::summarynav::userUID=" . $row['toUID'] . "::extra=" . $extra . ":]]";

			$onClick = "messages_addRecipient('" . $row['toUID'] . "');";

			$html .= ''
			 . "<table noborder>\n"
			 . "\t<tr>\n"
			 . "\t\t<td>"
			 . "\t\t\t<a href='javascript:void(0)' onClick=\"" . $onClick . "\">"
			 . "\t\t\t<img src='$arrowLeft' />"
			 . "\t\t</td>\n"
			 . "\t\t<td>[[:users::summarynav::userUID=" . $row['toUID'] . ":]]</td>\n"
			 . "\t</tr>\n"
			 . "</table>\n";

		}
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
