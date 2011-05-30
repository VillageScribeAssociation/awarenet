<?

		require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
		require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new forum thread
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not given.'); }
	if ('newThread' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('forum', $_POST)) { $page->do404('Forum not specified.'); }

	$forum = new Forums_Board($_POST['forum']);
	if (false == $forum->loaded) { $page->do404('Forum not found.'); }
	if (false == $user->authHas('forums', 'forums_board', 'makethread', $forum->UID)) 
		{ $page->do403('You cannot create new threads in this forum.'); }

	//----------------------------------------------------------------------------------------------
	//	create thread
	//----------------------------------------------------------------------------------------------

	$threadTitle = '';
	if (true == array_key_exists('title', $_POST)) { $threadTitle = $utils->cleanString($_POST['title']); }
	if ('' == trim($threadTitle)) { $threadTitle = '(No Subject)'; }

	$model = new Forums_Thread();
	$model->board = $forum->UID;
	$model->title = $threadTitle;
	$model->content = strip_tags($_POST['content']);	// TODO: allow some tags 
	$report = $model->save();

	// redirect back to forum if thread was nto created
	if ('' != $report) {
		$session->msg("Cound not create thread:<br/>$report", 'bad');
		$page->do302('forums/' . $forum->alias);
	} else {
		//------------------------------------------------------------------------------------------
		//	notify user's friends
		//------------------------------------------------------------------------------------------
		$ext = $model->extArray();
		$title = "New Forum Post: " . $ext['title'];
		$content = $model->content;

		$nUID = $notifications->create(
			'forums', 'forums_thread', $model->UID, 'forum_newthread', 
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
	$page->do302('forums/showthread/' . $model->alias);	

?>
