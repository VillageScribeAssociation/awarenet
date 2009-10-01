<?

	require_once($installPath . 'modules/chat/models/chat.mod.php');

//--------------------------------------------------------------------------------------------------
//	display contents of a single message queue, formatted as a table
//--------------------------------------------------------------------------------------------------
// * $args['user'] = UID of user to show queue for, default to current user

function chat_showqueue($args) {
	global $user;
	$html = '';
	$userUID = $user->data['UID'];
	if (array_key_exists('user', $args) == true) { $userUID = $args['user']; }	

	$html .= "<table noborder>\n";

	$model = new Chat($userUID);
	$model->expandMessages();
	foreach($model->messages as $msg) {
		$html .= "\t<tr>\n";
		$html .= "\t\t<td valign='top'>" . $msg['timestamp'] . "</td>\n";
		$html .= "\t\t<td valign='top'>" . $msg['from'] . "</td>\n";
		$html .= "\t\t<td valign='top'>" . $msg['content'] . "</td>\n";
		$html .= "\t</tr>\n";		
	}

	$html .= "</table>\n";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>