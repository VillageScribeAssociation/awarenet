<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');

//--------------------------------------------------------------------------------------------------
//	show a forum thread
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions ( TODO: banned, moderator, etc)
	//----------------------------------------------------------------------------------------------

	if (authHas('forums', 'show', '') == false) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	load thread and forum
	//----------------------------------------------------------------------------------------------

	$threadUID = raFindRedirect('forums', 'showthread', 'forumthreads', $request['ref']);

	$pageNo = 1;
	if (array_key_exists('page', $request['args']) == true) 
		{ $pageNo = floor($request['args']['page']); }

	$thread = new ForumThread($threadUID);
	$forum = new Forum($thread->data['forum']);

	$page->load($installPath . 'modules/forums/actions/showthread.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['threadUID'] = $threadUID;
	$page->blockArgs['forumUID'] = $forum->data['UID'];
	$page->blockArgs['pageno'] = $pageNo;
	$page->blockArgs['forumRa'] = $forum->data['recordAlias'];
	$page->blockArgs['forumTitle'] = $forum->data['title'];
	$page->blockArgs['threadRa'] = $thread->data['recordAlias'];
	$page->blockArgs['threadTitle'] = $thread->data['title'];
	$page->render();

?>
