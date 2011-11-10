<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an object is saved to database
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function moblog__cb_object_updated($args) {
	global $kapenta;
	global $db; 
	global $user;
	global $page;
	global $notifications;
	global $session;

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	if ('moblog' != $args['module']) { return false; }
	if ('moblog_post' != $args['model']) { return false; }

	$model = new Moblog_Post($args['UID']);

	//----------------------------------------------------------------------------------------------
	//	create or append notification
	//----------------------------------------------------------------------------------------------

	if ('yes' == $model->published) {
		//------------------------------------------------------------------------------------------
		//	check if this event was raised recently (within the hour)
		//------------------------------------------------------------------------------------------
		$recentUID = $notifications->existsRecent(
			'moblog', 'moblog_post', $model->UID, '*', 'moblog_editpost', (60 * 60)
		);

		$content = "" 
			. "[[:users::namelink::userUID=" . $model->createdBy . ":]] "
			. "has updated their blog post.";

		if ('' != $recentUID) {
			//--------------------------------------------------------------------------------------
			//	post was saved by the same user recently, update the notification
			//--------------------------------------------------------------------------------------
			$content = "<br/>\n$content<br/>\n<small>" . $model->editedOn . "</small>";
			$notifications->annotate($recentUID, $content);	
			$session->msg('annotating existing notification');

		} else {
			//--------------------------------------------------------------------------------------
			//	new event set, notify user's friends
			//--------------------------------------------------------------------------------------
			$ext = $model->extArray();
			$title = "Blog update: " . $ext['nameLink'];

			$nUID = $notifications->create(
				'moblog', 'moblog_post', $model->UID, 'moblog_editpost', 
				$title, $content, $ext['viewUrl']
			);
	
			$notifications->addFriends($nUID, $user->UID);
			$notifications->addAdmins($nUID, $user->UID);
		}
	}

	//----------------------------------------------------------------------------------------------
	//	raise a microbog event for this
	//----------------------------------------------------------------------------------------------
	if ('yes' == $model->published) {
		$message = '#' . $kapenta->websiteName . ' blog - '. $model->title;
		$mbargs = array(
			'refModule' => 'moblog',
			'refModel' => 'moblog_post',
			'refUID' => $model->UID,
			'message' => $message
		);
		$kapenta->raiseEvent('*', 'microblog_event', $mbargs);
	}

	//---------------------------------------------------------------------------------------------
	//	pull page triggers (DEPRECATED)
	//---------------------------------------------------------------------------------------------
	if ('moblog' == $args['module']) {
		$kapenta->logLive('in moblog callback, setting triggers ');
		$page->doTrigger('moblog', 'post-any');
		$page->doTrigger('moblog', 'post-' . $args['UID']);
		if (true == array_key_exists('createdBy', $args['data'])) {
			$page->doTrigger('moblog', 'post-by-' . $args['data']['createdBy']);
		}
	}

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
