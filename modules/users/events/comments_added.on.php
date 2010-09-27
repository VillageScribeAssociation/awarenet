<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when a comment is added
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached
//arg: refUID - UID of object to which comment was attached
//arg: commentUID - UID of the new comment
//arg: comment - text/html of comment

function users__cb_comments_added($refUID, $commentUID, $comment) {
	global $db, $user;
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('commentUID', $args)) { return false; }
	if (false == array_key_exists('comment', $args)) { return false; }

	if ($args['refModule'] != 'users') { return false; }
	if (false == $db->objectExists('Users_User', $args['refUID'])) { return false; }
	
	$model = new Users_User($args['refUID']);
	$creator = new Users_User($model->createdBy);

	//----------------------------------------------------------------------------------------------
	//	send notification to user
	//----------------------------------------------------------------------------------------------
	/* TODO: re-add notification
	$ext = $model->extArray();
	$link = "<a href='" . $ext['viewUrl'] . "#comment" . $commentUID ."'>wall</a>";
	$title = $user->getNameLink() . " commented on " . $model->getName() . "'s " . $link;
	$url = $ext['viewUrl'] . '#comment' . $commentUID;
	$imgUID = '';
	$imgUID = imgGetDefaultUID('projects', $refUID);

	if ($user->UID == $refUID) 
		{ $title = $user->getNameLink() . " commented on their own " . $link; }

	notifyFriends($refUID, $commentUID, 
				  $user->getName(), $user->getUrl(),
				  $title, $comment, $url, $imgUID );


	$title = $user->getNameLink() . " commented on your " . $link;
	if ($user->UID == $refUID) 
		{ $title = "You commented on their own " . $link; }

	notifyUser($refUID, $commentUID, 
				  $user->getName(), $user->getUrl(),
				  $title, $comment, $url, $imgUID );

	return true;
	*/
}

?>
