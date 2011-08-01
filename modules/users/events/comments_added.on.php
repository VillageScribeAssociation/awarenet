<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when a comment is added
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached [string]
//arg: refModel - Type of object to which comment was attached [string]
//arg: refUID - UID of object to which comment was attached [string]
//arg: commentUID - UID of the new comment [string]
//arg: comment - text/html of comment [string]

function users__cb_comments_added($args) {
	global $db;
	global $user;
	global $notifications;

	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('commentUID', $args)) { return false; }
	if (false == array_key_exists('comment', $args)) { return false; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if ($refModule != 'users') { return false; }
	if (false == $db->objectExists('users_user', $refUID)) { return false; }
	
	$model = new Users_User($refUID);
	$creator = new Users_User($model->createdBy);

	//----------------------------------------------------------------------------------------------
	//	send notification to user
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$link = "<a href='" . $ext['viewUrl'] . "#comment" . $args['commentUID'] ."'>wall</a>";
	$title = $user->getName() . " commented on " . $model->getName() . "'s wall.";
	$url = $ext['viewUrl'] . '#comment' . $args['commentUID'];

	$nUID = $notifications->create(
		$refModule, 
		$refModel, 
		$refUID, 
		'comments_added', 
		$title, 
		$args['comment'], 
		$url
	);

	$notifications->addUser($nUID, $user->UID );
	$notifications->addFriends($nUID, $user->UID );

	return true;

}

?>
