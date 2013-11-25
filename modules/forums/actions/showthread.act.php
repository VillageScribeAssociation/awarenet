<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a forum thread
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$pageNo = 1;		//% default page to show [int]

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference ( TODO: banned, moderator, etc)
	//----------------------------------------------------------------------------------------------
	$threadUID = $aliases->findRedirect('forums_thread');
	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = floor($kapenta->request->args['page']); }

	$thread = new Forums_Thread($threadUID);
	$forum = new Forums_Board($thread->board);

	if (false == $user->authHas('forums', 'forums_thread', 'show', $thread->UID)) { $page->do403();}
	if (false == $user->authHas('forums', 'forums_board', 'show', $forum->UID)) { $page->do403(); }	

	//----------------------------------------------------------------------------------------------
	//	redner the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/forums/actions/showthread.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['threadUID'] = $threadUID;
	$kapenta->page->blockArgs['forumUID'] = $forum->UID;
	$kapenta->page->blockArgs['pageno'] = $pageNo;
	$kapenta->page->blockArgs['forumRa'] = $forum->alias;
	$kapenta->page->blockArgs['forumTitle'] = $forum->title;
	$kapenta->page->blockArgs['threadRa'] = $thread->alias;
	$kapenta->page->blockArgs['threadTitle'] = $thread->title;
	$kapenta->page->blockArgs['createdBy'] = $thread->createdBy;
	$kapenta->page->render();

?>
