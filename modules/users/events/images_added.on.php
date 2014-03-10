<?

//-------------------------------------------------------------------------------------------------
//|	fired when an image is added 
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached
//arg: refUID - UID of object to which comment was attached
//arg: imageUID - UID of the new comment
//arg: imageTitle - text/html of comment

function users__cb_images_added($args) {
		global $kapenta;
		global $db;
		global $user;
		global $notifications;
		global $theme;

	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('imageUID', $args)) { return false; }
	if (false == array_key_exists('imageTitle', $args)) { return false; }

	if ($args['refModule'] != 'users') { return false; }

	//----------------------------------------------------------------------------------------------
	//	create notification and send to friends
	//----------------------------------------------------------------------------------------------
	$url = '%%serverPath%%users/profile/' . $args['refUID'];
	$link = "<a href='" . $url . "'>" . $args['imageTitle'] . "</a>";
	$title = $user->getName() . ' added a new profile picture.';

	$content = ''
	 . "<a href='%%serverPath%%users/profile/" . $args['refUID'] . "'>"
	 . "[[:images::width300::raUID=" . $args['imageUID'] . "::link=no:]]"
	 . "</a>\n<!-- more images -->";

	$nUID = $notifications->create(
		'users', 'users_user', $args['refUID'], 'images_added', $title, $content, $url
	);

	$notifications->addUser($nUID, $user->UID);
	$notifications->addFriends($nUID, $user->UID);
	$notifications->addFriends($nUID, $user->UID);
	$notifications->addSchoolGrade($nUID, $user->school, $user->grade);

	//----------------------------------------------------------------------------------------------
	//	raise event to set this as the default image for this object
	//----------------------------------------------------------------------------------------------
	$kapenta->raiseEvent('*', 'images_isdefault', $args);

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return true;
}

//-------------------------------------------------------------------------------------------------
?>
