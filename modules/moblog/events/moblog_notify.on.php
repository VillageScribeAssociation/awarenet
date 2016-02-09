<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//|	fired asynchronously to update notification feeds in background
//--------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function moblog__cb_moblog_notify($args) {
	global $kapenta;
	global $notifications;
	global $session;
	global $cache;

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	raise notifications and microblog
	//----------------------------------------------------------------------------------------------

	$model = new Moblog_Post($args['UID']);

	//----------------------------------------------------------------------------------------------
	//	invalidate cached views
	//----------------------------------------------------------------------------------------------
	$cache->clear('moblog-summarynav-' . $model->UID);
	$cache->clear('moblog-summary-' . $model->UID);
	$cache->clear('moblog-show-' . $model->UID);
	$cache->clear('moblog-schoolstatsnav');

	//----------------------------------------------------------------------------------------------
	//	create or append notification
	//----------------------------------------------------------------------------------------------

	if (('yes' == $model->published) && ($kapenta->user->UID == $model->createdBy)) {
		//------------------------------------------------------------------------------------------
		//	check if this event was raised recently (within the hour)
		//------------------------------------------------------------------------------------------
		$recentUID = $notifications->existsRecent(
			'moblog', 'moblog_post', $model->UID, '*', 'moblog_editpost', (60 * 60)
		);

		$content = "" 
			. "[[:users::namelink::userUID=" . $model->createdBy . ":]] "
			. "has updated their blog post.)";

		if ('' != $recentUID) {
			//--------------------------------------------------------------------------------------
			//	post was saved by the same user recently, update the notification
			//--------------------------------------------------------------------------------------
			$content = "<br/>\n$content<br/>\n<small>" . $model->editedOn . "</small>";
			$notifications->annotate($recentUID, $content);	
			$kapenta->session->msg('annotating existing notification');

		} else {
			//--------------------------------------------------------------------------------------
			//	new event set, notify user's friends
			//--------------------------------------------------------------------------------------
			$ext = $model->extArray();
			$title = "Blog update: " . $ext['title'];

			$nUID = $notifications->create(
				'moblog', 'moblog_post', $model->UID, 'moblog_editpost', 
				$title, $content, $ext['viewUrl']
			);
	
			$notifications->addFriends($nUID, $kapenta->user->UID);
			$notifications->addAdmins($nUID, $kapenta->user->UID);
		}
	}

	//----------------------------------------------------------------------------------------------
	//	raise a microbog event for this
	//----------------------------------------------------------------------------------------------
	if (('yes' == $model->published) && ($kapenta->user->UID == $model->createdBy)) {
		$message = '#' . $kapenta->websiteName . ' blog - '. $model->title;
		$mbargs = array(
			'refModule' => 'moblog',
			'refModel' => 'moblog_post',
			'refUID' => $model->UID,
			'message' => $message
		);
		$kapenta->raiseEvent('*', 'microblog_event', $mbargs);
	}

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
