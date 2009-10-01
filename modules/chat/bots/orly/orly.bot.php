<?

//--------------------------------------------------------------------------------------------------
//	dummy bot to test chatbot API
//--------------------------------------------------------------------------------------------------

function chat_bot_orly_submit($msg, $recipient) {
	global $serverPath;
	$retVal = array();

	//----------------------------------------------------------------------------------------------
	//	make response
	//----------------------------------------------------------------------------------------------

	$url = $serverPath . 'modules/chat/bots/orly/orly.jpg';
	$html = "<img src='" . $url . "' width='180' height='180' />";

	$retVal['sender'] = $html;
	$retVal['recipient'] = $html;

	return $retVal;
}

function chat_bot_orly_help($msg) {
	return '<font color=black>chicken is a type of pie.</font>';
}

?>
