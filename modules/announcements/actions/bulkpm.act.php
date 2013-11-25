<?php

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//*	Action to spam members of a school or group with an announcement
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Announcement UID not given.'); }

	$model = new Announcements_Announcement($_POST['UID']);
	if (false == $model->loaded) { $page->do404("Announcement not found."); }

	if (false == $user->authHas('announcements', 'announcements_announcement', 'spam', $model->UID)) {
		$page->do403('You do not have bulk PM permissions.');
	}

	//----------------------------------------------------------------------------------------------
	//	get list of members to PM
	//----------------------------------------------------------------------------------------------

	$userList = array();

	if ('schools_school' == $model->refModel) {
		$sql = ''
		 . "SELECT UID FROM `users_user`"
		 . " WHERE `school`='" . $db->addMarkup($model->refUID) . "'";

		$result = $db->query($sql);
		while ($row = $db->fetchAssoc($result)) { $userList[] = $row['UID']; }
	}

	if ('groups_group' == $model->refModel) {
		$sql = ''
		 . "SELECT userUID FROM `groups_membership`"
		 . " WHERE `groupUID`='" . $db->addMarkup($model->refUID) . "'";
		
		$result = $db->query($sql);
		while($row = $db->fetchAssoc($result)) {
			if (false == in_array($row['userUID'], $userList)) { $userList[] = $row['userUID']; }
		}
	}

	//----------------------------------------------------------------------------------------------
	//	send the messages
	//----------------------------------------------------------------------------------------------
	
	$db->transactionStart();

	foreach($userList as $UID) {
		$userName = $theme->expandBlocks('[[:users::name::userUID=' . $UID . ':]]', 'nav1');

		$detail = array(
			'fromUID' => $user->UID,
			'toUID' => $UID,
			'title' => 'Announcement: ' . $model->title,
			'content' => $model->content,
			're' => ''
		);

		$outcome = $kapenta->raiseEvent('messages', 'messages_send', $detail);

		if ((true == array_key_exists('messages', $outcome)) && (true == $outcome['messages'])) {
			$session->msg("Sending message to member: $userName ($UID)\n", 'ok');
		} else {
			$session->msg("Sending message to member: $userName ($UID)\n", 'ok');
		}

	}

	$db->transactionEnd();

	//----------------------------------------------------------------------------------------------
	//	redirect to the user's outbox
	//----------------------------------------------------------------------------------------------

	$page->do302('messages/outbox/');

?>
