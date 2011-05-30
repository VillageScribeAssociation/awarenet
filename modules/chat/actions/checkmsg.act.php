<?

//--------------------------------------------------------------------------------------------------
//	send messages for the current user
//--------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/chat/models/chat.mod.php');
	$queue = new Chat($user->UID);

	//----------------------------------------------------------------------------------------------
	//	make sure public user cannot chat
	//----------------------------------------------------------------------------------------------

	if ($user->role == 'public') { 
		// close all open windows and die
		setcookie('chatwindows', '');
		echo " "; flush(); die();
	}

	//----------------------------------------------------------------------------------------------
	//	update user's lastonline field
	//----------------------------------------------------------------------------------------------

	$sql = "update users_user set lastOnline='" . $db->datetime() . "' "
		 . "where UID='" . $db->addMarkup($user->UID) . "'";

	$db->query($sql);	

	//----------------------------------------------------------------------------------------------
	//	get time of last message received by the client
	//----------------------------------------------------------------------------------------------

	if (array_key_exists('since', $req->args) == false) { echo " "; flush(); die(); }
	$since = floor($req->args['since']);

	//----------------------------------------------------------------------------------------------
	//	send all messages more recent than that
	//----------------------------------------------------------------------------------------------

	echo "queue contains " . count($queue->messages) . " messages...<br/>\n";

	$js = '';
	foreach($queue->messages as $key => $msg) {
		if ((floor($msg['timestamp']) >= $since) && ($msg['UID'] != '')) {

			$safeContent = chatRemoveMarkup($msg['content']);

			$js .= "\twindow.parent.messageAdd("
				 . "\"" . $msg['UID'] . "\", "
				 . "\"" . $msg['from'] . "\", "
				 . "\"" . gmdate("l, Y-m-j H:i:s", $msg['timestamp']) . "\", "
				 . "\"" . $msg['timestamp'] . "\", "
				 . "\"" . $safeContent . "\","
				 . "\"" . $msg['mine']. "\""
				 . ");\n";
		}
	}

	$html = "
<html>
<head>
<script language='Javascript'>
	$js;
</script>
</head>
<body>
</body>
</html>";

	echo $html;

//----------------------------------------------------------------------------------------------
//	chat utility functions
//----------------------------------------------------------------------------------------------
//	mark up HTML so that it doesn't form child elements in the chatqueue XML
//	DEPRECATED, TODO: fidn out if anything still uses this after switching from iframes

//function chatMarkup($txt) {
//	$txt = str_replace('<', '{*[', $txt);	
//	$txt = str_replace('>', ']*}', $txt);
//	$txt = str_replace("\"", "'", $txt);		// javascript safe
//	return $txt;
//}

function chatRemoveMarkup($txt) {
	$txt = str_replace('{*[', '<', $txt);	
	$txt = str_replace(']*}', '>', $txt);	
	return $txt;
}

?>
