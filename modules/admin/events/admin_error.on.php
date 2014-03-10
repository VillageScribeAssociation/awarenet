<?php

//--------------------------------------------------------------------------------------------------
//*	fired when soem component raises the admin_error event
//--------------------------------------------------------------------------------------------------
//arg: source - file and method reporting the error [string]
//arg: message - detail of error message [string]

function admin__cb_admin_error($args) {
	global $kapenta;
	global $user;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('source', $args)) { return false; }
	if (false == array_key_exists('message', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	send a pm to all the admins
	//----------------------------------------------------------------------------------------------
	
	$range = $kapenta->db->loadRange('users_user', '*', array("role='admin'"));

	foreach($range as $item) {

		$content = ''
		 . '<b>error message:</b> ' . $args['message'] . "<br/>\n"
		 . '<b>source:</b> ' . $args['source'] . "<br/>\n"
		 . "<small>Automatic error message, sender reflects user context it occured in.</small>";

		//arg: fromUID - UID of a Users_User object [string]
		//arg: toUID - UID of a Users_User object [string]
		//arg: subject - title of message [string]
		//arg: content - body of message (html) [string]
		//opt: re - UID of a messages_message object this is in reply to [string]

		$detail = array(
			'fromUID' => $user->UID,
			'toUID' => $item['UID'],
			'title' => 'Kapenta Error Message',
			'content' => $content
		);

		$kapenta->raiseEvent('messages', 'messages_send', $detail);
	}

}

?>
