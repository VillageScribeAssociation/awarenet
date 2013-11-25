<?php

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	send a PM on behalf of a user
//--------------------------------------------------------------------------------------------------
//arg: fromUID - UID of a Users_User object [string]
//arg: toUID - UID of a Users_User object [string]
//arg: title - title of message [string]
//arg: content - body of message (html) [string]
//opt: re - UID of a messages_message object this is in reply to [string]

function messages__cb_messages_send($args) {
	global $db;
	global $user;
	global $theme;
	global $utils;
	global $kapenta;
	global $session;

	//echo "received messages_send event.<br/>\n";
	//print_r($args);

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return false; }

	if (false == array_key_exists('fromUID', $args)) { return false; }
	if (false == array_key_exists('toUID', $args)) { return false; }
	if (false == array_key_exists('title', $args)) { return false; }
	if (false == array_key_exists('content', $args)) { return false; }

	$fromUID = $args['fromUID'];
	$toUID = $args['toUID'];

	$title = $utils->cleanString($args['title']);
	if ('' == trim($title)) { $title = '(no subject)'; }

	$content = $utils->cleanHtml($args['content']);

	if (false == $db->objectExists('users_user', $fromUID)) { return false; }	//	must exist
	if (false == $db->objectExists('users_user', $toUID)) { return false; }		//	must exist

	//echo "passed basic tests.<br/>\n";

	//----------------------------------------------------------------------------------------------
	//	send message to recipient
	//----------------------------------------------------------------------------------------------

	$toNameBlock = '[[:users::name::userUID=' . $toUID . ':]]';
	$toName = $theme->expandBlocks($toNameBlock, '');

	//TODO: more sanitization here
	$model = new Messages_Message();
	$model->owner = $toUID;
	$model->folder = 'inbox';
	$model->fromUID = $user->UID;
	$model->fromName = $user->getName();
	$model->toUID = $toUID;
	$model->toName = $toName;
	$model->cc = '';
	$model->title = $title;
	$model->content = $content;
	$model->status = 'unread';

	//	Asynchronous version, send database update via message queue, better for slow dbs
	$detail = array('model' => 'messages_message', 'data' => $model->toArray());
	$kapenta->raiseEvent('p2p', 'p2p_selfcast', $detail);	

	//	Synchronous version, uncomment to write directly to database
	/*
	$report = $model->save();
	if ('' != $report) {
		$session->msg("Could not send message to $toName:<br/>$report", 'bad');
		return false;
	}
	*/

	//----------------------------------------------------------------------------------------------
	//	save a copy in the sender's outbox
	//----------------------------------------------------------------------------------------------

	$model->UID = $kapenta->createUID();
	$model->owner = $user->UID;
	$model->folder = "outbox";

	//	Asynchronous version, send database update via message queue, better for slow dbs
	$detail = array('model' => 'messages_message', 'data' => $model->toArray());
	$kapenta->raiseEvent('p2p', 'p2p_selfcast', $detail);	

	//	Synchronous version, uncomment to write directly to database
	/*
	$report = $model->save();
	if ('' != $report) {
		$session->msg("Could not save owner's copy of message:<br/>$report", 'bad');
		return false;
	}
	$session->msg('PM sent to: ' . $toName . " (" . $model->UID . ")", 'bad');
	*/

	//----------------------------------------------------------------------------------------------
	//	invalidate cached inboxcount
	//----------------------------------------------------------------------------------------------
	if (true == $kapenta->mcEnabled) { $kapenta->cacheDelete('pmcount::' . $model->owner); }
	if (true == $kapenta->mcEnabled) { $kapenta->cacheDelete('pmcount::' . $model->fromUID); }
	if (true == $kapenta->mcEnabled) { $kapenta->cacheDelete('pmcount::' . $model->toUID); }

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------

	$session->msg('PM sent to: ' . $toName . ", it may take a minute or two to arrive.", 'ok');

	return true;
}

?>
