<?

require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when a comment is added
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached
//arg: refUID - UID of object to which comment was attached
//arg: commentUID - UID of the new comment
//arg: comment - text/html of comment

function projects__cb_comments_added($args) {
	global $db, $user;
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('commentUID', $args)) { return false; }
	if (false == array_key_exists('comment', $args)) { return false; }

	if ($args['refModule'] != 'projects') { return false; }
	
	$model = new Projects_Project($args['refUID']);
	if (false == $model->loaded) { return false; }
	$u = new Users_User($model->createdBy);
	if (false == $u->loaded) { return false; }
	
	//----------------------------------------------------------------------------------------------
	//	send notifications to project members
	//----------------------------------------------------------------------------------------------
	/*	TODO: re-add notifications
	$ext = $model->extArray();
	$commentUrl = $ext['viewUrl'] . "#comment" . $args['commentUID'];

	$link = "<a href='" . $commentUrl ."'>" . $ext['title'] . "</a>";
	$title = $user->getNameLink() . " commented on " . $u->getName() . "'s blog post " . $link;
	$url = $ext['viewUrl'] . '#comment' . $args['commentUID'];
	$imgUID = imgGetDefaultUID('moblog', $args['refUID']);

	if ($user->UID == $u->UID) 
		{ $title = $user->getNameLink() . " commented on their own blog post " . $link; }

	notifyFriends($args['refUID'], $args['commentUID'], 
				  $user->getName(), $user->getUrl(),
				  $title, $args['comment'], $url, $imgUID );

	$title = $user->getNameLink() . " commented on your blog post " . $link;
	notifyUser($args['refUID'], $args['commentUID'], 
				  $user->getName(), $user->getUrl(),
				  $title, $args['comment'], $url, $imgUID );
	*/

	return true;
}


//-------------------------------------------------------------------------------------------------
?>
