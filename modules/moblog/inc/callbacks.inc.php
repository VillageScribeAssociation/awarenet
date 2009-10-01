<?

//--------------------------------------------------------------------------------------------------
//	callbacks allow modules to interact with being necessarily dependant on one another
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/moblog/models/moblog.mod.php');

//--------------------------------------------------------------------------------------------------
//	when a comment is posted
//--------------------------------------------------------------------------------------------------

function moblog__cb_comments_add($refUID, $commentUID, $comment) {
	global $user;
	if (dbRecordExists('users', $refUID) == false) { return false; }
	$model = new Moblog($refUID);
	$u = new Users($model->data['createdBy']);

	//----------------------------------------------------------------------------------------------
	//	send notifications to project members
	//----------------------------------------------------------------------------------------------

	$ext = $model->extArray();
	$link = "<a href='" . $ext['viewUrl'] . "#comment" . $commentUID ."'>" . $ext['title'] . "</a>";
	$title = $user->getNameLink() . " commented on " . $u->getName() . "'s blog post " . $link;
	$url = $ext['viewUrl'] . '#comment' . $commentUID;
	$imgUID = '';
	$imgUID = imgGetHeaviestUID('moblog', $refUID);

	if ($user->data['UID'] == $u->data['UID']) 
		{ $title = $user->getNameLink() . " commented on their own blog post " . $link; }

	notifyFriends($refUID, $commentUID, 
				  $user->getName(), $user->getUrl(),
				  $title, $comment, $url, $imgUID );


	$title = $user->getNameLink() . " commented on your blog post " . $link;
	notifyUser($refUID, $commentUID, 
				  $user->getName(), $user->getUrl(),
				  $title, $comment, $url, $imgUID );

	return true;
}

?>
