<?php

//--------------------------------------------------------------------------------------------------
//*	fired when a friend request is confirmed
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of the user who initiated the relationship [string]
//arg: friendUID - UID of reciprocating user [string]
//arg: relationship - label, eg 'friend', 'spouse', 'penpal' [string]

function users__cb_friendship_created($args) {
	global $notifications;
	global $user;
	global $db;

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return false; }

	if (false == array_key_exists('userUID', $args)) { return false; }
	if (false == array_key_exists('friendUID', $args)) { return false; }
	if (false == array_key_exists('relationship', $args)) { return false; }

	if (false == $db->objectExists('users_user', $args['userUID'])) { return false; }
	if (false == $db->objectExists('users_user', $args['friendUID'])) { return false; }  

	$fromUser = new Users_User($args['userUID']);
	$toUser = new Users_User($args['friendUID']);

	//----------------------------------------------------------------------------------------------
	//	create notification
	//----------------------------------------------------------------------------------------------

	$title = $fromUser->getName() . ' and ' . $toUser->getName() . ' are friends.';

	$content = 'relationship: ' . $args['relationship'];

	$nUID = $notifications->create(
		'users', 'users_user', $args['userUID'], 'friendship_created', 
		$title, $content, $url = '%%serverPath%%users/profile/' . $toUser->alias, 
		$private = false
	);

	//----------------------------------------------------------------------------------------------
	//	notify friends and classmates of both users
	//----------------------------------------------------------------------------------------------
	
	$notifications->addSchoolGrade($nUID, $fromUser->school, $fromUser->grade);
	$notifications->addSchoolGrade($nUID, $toUser->school, $toUser->grade);

	$notifications->addFriends($nUID, $fromUser->UID);
	$notifications->addFriends($nUID, $toUser->UID);

	//	not sure this necessary or appropriate, added on request as a temporary measure to assure
	//	admins can see this is working, and show realtime activity:
	$notifications->addAdmins($nUID);

	return true;
}

?>
