<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//-------------------------------------------------------------------------------------------------
//*	fired when a comment is added
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached [string]
//arg: refModel - type of object which owns comment [string]
//arg: refUID - UID of object to which comment was attached [string]
//arg: commentUID - UID of the new comment [string]
//arg: comment - text/html of comment [string]

function videos__cb_comments_added($args) {
	global $kapenta;
	global $kapenta;
	global $user;
	global $theme;
	global $notifications;

	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('commentUID', $args)) { return false; }
	if (false == array_key_exists('comment', $args)) { return false; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if ('videos' != $refModule) { return false; }
	if (false == $kapenta->db->objectExists('videos_video', $refUID)) { return false; }
	
	$model = new Videos_Video($refUID);
	if (false == $model->loaded) { return false; }
	$u = new Users_User($model->createdBy);

	//----------------------------------------------------------------------------------------------
	//	create notification
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$title = $user->getName() . " commented on " . $u->getName() . "'s video: " . $model->title;
	$url = $ext['viewUrl'] . '#comment' . $args['commentUID'];

	if ('videos_gallery' == $model->refModel) {
		$url = '%%serverPath%%videos/play/' . $model->alias . '#comment' . $args['commentUID'];
	}

	if ($user->UID == $u->UID) { 
		$title = $user->getName() . " commented on their own video: " . $model->title; 
	}

	$nUID = $notifications->create(
		'videos', 'videos_video', $refUID, 'comments_added', $title, $args['comment'], $url
	);

	//----------------------------------------------------------------------------------------------
	//	send notifications to blogger and their friends
	//----------------------------------------------------------------------------------------------
	$notifications->addUser($nUID, $user->UID);
	$notifications->addUser($nUID, $u->UID);
	$notifications->addFriends($nUID, $user->UID);
	$notifications->addFriends($nUID, $u->UID);
	$notifications->addAdmins($nUID);

	//----------------------------------------------------------------------------------------------
	//	send notifications to anyone else who has commented on this post
	//----------------------------------------------------------------------------------------------
	$ea = array(
		'refModule' => $refModule,
		'refModel' => $refModel,
		'refUID' => $refUID,
		'notificationUID' => $nUID
	);

	$kapenta->raiseEvent('comments', 'notify_commenters', $ea);

	return true;
}

//--------------------------------------------------------------------------------------------------
?>
