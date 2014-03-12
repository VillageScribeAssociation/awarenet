<?

		require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
		require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new forum thread
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not given.'); }
	if ('newThread' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('forum', $_POST)) { $kapenta->page->do404('Forum not specified.'); }

	$forum = new Forums_Board($_POST['forum']);
	if (false == $forum->loaded) { $kapenta->page->do404('Forum not found.'); }
	if (false == $kapenta->user->authHas('forums', 'forums_board', 'makethread', $forum->UID)) {
		$kapenta->page->do403('You cannot create new threads in this forum.'); 
	}

	//----------------------------------------------------------------------------------------------
	//	create thread
	//----------------------------------------------------------------------------------------------
	$model = new Forums_Thread();
	$model->board = $forum->UID;
	
	foreach($_POST as $key => $value) {
		switch($key) {
			case 'title':		$model->title = $utils->cleanTitle($value); 	break;
			case 'content':		$model->content = $utils->cleanHtml($value);	break;
		}
	}

	if ('' == trim($model->title)) { $model->title = '(No Subject)'; }
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	raise events and redirect to thread
	//----------------------------------------------------------------------------------------------
	if ('' != $report) {
		//------------------------------------------------------------------------------------------
		// redirect back to forum if thread was not created
		//------------------------------------------------------------------------------------------
		$kapenta->session->msg("Cound not create thread:<br/>$report", 'bad');
		$kapenta->page->do302('forums/' . $forum->alias);

	} else {
		//------------------------------------------------------------------------------------------
		//	notify user's friends
		//------------------------------------------------------------------------------------------
		//TODO: move this to an event handler
		$ext = $model->extArray();
		$title = "New Forum Post: " . $ext['title'];
		$content = $model->content;

		$nUID = $notifications->create(
			'forums', 'forums_thread', $model->UID, 'forum_newthread', 
			$title, $content, $ext['viewUrl']
		);

		$notifications->addFriends($nUID, $kapenta->user->UID);
		$notifications->addAdmins($nUID, $kapenta->user->UID);

		//------------------------------------------------------------------------------------------
		//	raise a microbog event for this
		//------------------------------------------------------------------------------------------
		$args = array(
			'refModule' => 'forums',
			'refModel' => 'forums_thread',
			'refUID' => $model->UID,
			'message' => '#' . $kapenta->websiteName . ' new forum thread - ' . $model->title
		);

		$kapenta->raiseEvent('*', 'microblog_event', $args);
	}

	//----------------------------------------------------------------------------------------------
	//	increment thread count on the forum
	//----------------------------------------------------------------------------------------------
	$forum->threads += 1;
	$forum->save();

	//----------------------------------------------------------------------------------------------
	//	redirect to new thread
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('forums/showthread/' . $model->alias);	

?>
