<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//-------------------------------------------------------------------------------------------------
//*	fired when a comment is added
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached [string]
//arg: refModel - type of object which owns comment [string]
//arg: refUID - UID of object to which comment was attached [string]
//arg: commentUID - UID of the new comment [string]
//arg: comment - text/html of comment [string]

function moblog__cb_comments_added($args) {
	global $kapenta;
	global $kapenta;
	global $kapenta;
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

	if ('moblog' != $refModule) { return false; }
	if (false == $kapenta->db->objectExists('moblog_post', $refUID)) { return false; }
	
	$model = new Moblog_Post($refUID);
	if (false == $model->loaded) { return false; }
	$u = new Users_User($model->createdBy);

	//----------------------------------------------------------------------------------------------
	//	update comment count on Moblog_Post object
	//----------------------------------------------------------------------------------------------
	$block = "[[:comments::count::refModule=moblog::refModel=moblog_post::refUID=$refUID:]]";
	$total = $theme->expandBlocks($block, '');
	$model->commentCount = (int)$total;
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	create notification
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$title = $kapenta->user->getName() . " commented on " . $u->getName() . "'s blog post: " . $model->title;
	$url = $ext['viewUrl'] . '#comment' . $args['commentUID'];

	if ($kapenta->user->UID == $u->UID) { 
		$title = $kapenta->user->getName() . " commented on their own blog post: " . $model->title; 
	}

	$nUID = $notifications->create(
		'moblog', 'moblog_post', $refUID, 'comments_added', $title, $args['comment'], $url
	);

	//----------------------------------------------------------------------------------------------
	//	send notifications to blogger and their friends
	//----------------------------------------------------------------------------------------------
	$notifications->addUser($nUID, $kapenta->user->UID);
	$notifications->addUser($nUID, $u->UID);
	$notifications->addFriends($nUID, $kapenta->user->UID);
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
