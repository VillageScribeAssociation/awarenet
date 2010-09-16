<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new reply to a forum thread
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('newThreadReply' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('thread', $_POST)) { $page->do404('Thread not specified.'); }

	$thread = new Forums_Thread($_POST['thread']);
	if (false == $thread->loaded) { $page->do404(); }
	if (false == $user->authHas('forums', 'Forums_Reply', 'new', $thread->UID))
		{ $page->do403(); }

	//------------------------------------------------------------------------------------------
	//	add the reply
	//------------------------------------------------------------------------------------------
	$reply = new Forums_Reply();
	$reply->thread = $_POST['thread'];
	$reply->content = $_POST['content'];
	$report = $reply->save();
	if ('' != $report) { $session->msg('Could not add reply: <br/>' . $report, 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	increment reply count on thread
	//----------------------------------------------------------------------------------------------
	$thread->replies += 1;
	$thread->updated = $db->datetime();
	$thread->save();

	//----------------------------------------------------------------------------------------------
	//	increment reply count on forum
	//----------------------------------------------------------------------------------------------
	$forum = new Forums_Board($thread->board);
	//TODO: add a method to board to count replies
	$forum->replies += 1;
	$forum->save();

	//----------------------------------------------------------------------------------------------
	//	send a notification to poster
	//----------------------------------------------------------------------------------------------
	//TODO:	send a notification to poster

	//----------------------------------------------------------------------------------------------
	//	redirect back to thread
	//----------------------------------------------------------------------------------------------
	$page->do302('forums/showthread/' . $thread->alias);

?>
