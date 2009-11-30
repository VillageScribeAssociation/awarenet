<?

require_once($installPath . 'modules/moblog/models/moblog.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when a comment is added
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached
//arg: refUID - UID of object to which comment was attached
//arg: commentUID - UID of the new comment
//arg: comment - text/html of comment

function moblog__cb_comments_added($args) {
	global $user;
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	if (array_key_exists('commentUID', $args) == false) { return false; }
	if (array_key_exists('comment', $args) == false) { return false; }

	if ($args['refModule'] != 'moblog') { return false; }
	if (dbRecordExists('users', $args['refUID']) == false) { return false; }
	
	$model = new Moblog($args['refUID']);
	$u = new Users($model->data['createdBy']);

	//----------------------------------------------------------------------------------------------
	//	send notifications to blogger and their friends
	//----------------------------------------------------------------------------------------------

	$ext = $model->extArray();
	$commentUrl = $ext['viewUrl'] . "#comment" . $args['commentUID'];

	$link = "<a href='" . $commentUrl ."'>" . $ext['title'] . "</a>";
	$title = $user->getNameLink() . " commented on " . $u->getName() . "'s blog post " . $link;
	$url = $ext['viewUrl'] . '#comment' . $args['commentUID'];
	$imgUID = imgGetDefaultUID('moblog', $args['refUID']);

	if ($user->data['UID'] == $u->data['UID']) 
		{ $title = $user->getNameLink() . " commented on their own blog post " . $link; }

	notifyFriends($args['refUID'], $args['commentUID'], 
				  $user->getName(), $user->getUrl(),
				  $title, $args['comment'], $url, $imgUID );


	$title = $user->getNameLink() . " commented on your blog post " . $link;
	notifyUser($args['refUID'], $args['commentUID'], 
				  $user->getName(), $user->getUrl(),
				  $title, $args['comment'], $url, $imgUID );

	return true;
}


//-------------------------------------------------------------------------------------------------
?>
