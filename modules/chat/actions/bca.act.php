<?

//-------------------------------------------------------------------------------------------------
//	broadcast a test message to admin from Chisai.Gondo
//-------------------------------------------------------------------------------------------------

	$msgUID = $kapenta->createUID();
	$cfUID = '748248257198046057';

	$msg = base64_encode('this is a test message ' . rand());
	$msg = base64_encode($msgUID . '|' . $cfUID . '|' . $db->datetime() . '|' . time() . '|' . $msg);

	notifyChannel('chat-user-admin', 'add', $msg);

	echo "sent notification: $msg \n";	

?>
