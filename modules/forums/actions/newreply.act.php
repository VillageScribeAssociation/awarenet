<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new reply to a forum thread
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('newThreadReply' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('thread', $_POST)) { $kapenta->page->do404('Thread not specified.'); }

	$thread = new Forums_Thread($_POST['thread']);
	if (false == $thread->loaded) { $kapenta->page->do404('Thread not found.'); }
	if (false == $user->authHas('forums', 'forums_reply', 'new', $thread->UID)) { 
		$kapenta->page->do403('You are not permitted to post in this thread.'); 
	}

	//------------------------------------------------------------------------------------------
	//	add the reply
	//------------------------------------------------------------------------------------------
	$reply = new Forums_Reply();
	$reply->thread = $thread->UID;
	$reply->content = $utils->cleanHtml($_POST['content']);
	$report = $reply->save();
	if ('' != $report) { $session->msg('Could not add reply: <br/>' . $report, 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	increment reply count on thread
	//----------------------------------------------------------------------------------------------
	$thread->replies += 1;
	$thread->updated = $kapenta->db->datetime();
	$thread->save();

	//----------------------------------------------------------------------------------------------
	//	increment reply count on forum
	//----------------------------------------------------------------------------------------------
	$forum = new Forums_Board($thread->board);
	//TODO: add a method to board to count replies
	$forum->replies += 1;
	$forum->save();

	//----------------------------------------------------------------------------------------------
	//	send a notification to poster's friends and other members of the thread
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		//------------------------------------------------------------------------------------------
		//	notify user's friends
		//------------------------------------------------------------------------------------------
		$ext = $thread->extArray();

		$title = "New Forum Reply: " . $thread->title;
		$content = $reply->content;
		$url = $ext['viewUrl'];

		$nUID = $notifications->create(
			'forums', 'forums_reply', $reply->UID, 'forum_newreply', 
			$title, $content, $ext['viewUrl']
		);

		$notifications->addFriends($nUID, $user->UID);
		$notifications->addAdmins($nUID, $user->UID);

		//------------------------------------------------------------------------------------------
		//	raise a microbog event for this
		//------------------------------------------------------------------------------------------
		$args = array(
			'refModule' => 'forums',
			'refModel' => 'forums_thread',
			'refUID' => $thread->UID,
			'message' => '#'. $kapenta->websiteName .' new reply to forum thread - '. $thread->title
		);

		$kapenta->raiseEvent('*', 'microblog_event', $args);
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to thread
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('forums/showthread/' . $thread->alias);

?>
