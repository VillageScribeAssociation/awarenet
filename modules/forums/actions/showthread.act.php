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
	if (true == array_key_exists('page', $req->args)) { $pageNo = floor($req->args['page']); }

	$thread = new Forums_Thread($threadUID);
	$forum = new Forums_Board($thread->board);

	if (false == $user->authHas('forums', 'forums_thread', 'show', $thread->UID)) { $page->do403();}
	if (false == $user->authHas('forums', 'forums_board', 'show', $forum->UID)) { $page->do403(); }	

	//----------------------------------------------------------------------------------------------
	//	redner the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/forums/actions/showthread.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['threadUID'] = $threadUID;
	$page->blockArgs['forumUID'] = $forum->UID;
	$page->blockArgs['pageno'] = $pageNo;
	$page->blockArgs['forumRa'] = $forum->alias;
	$page->blockArgs['forumTitle'] = $forum->title;
	$page->blockArgs['threadRa'] = $thread->alias;
	$page->blockArgs['threadTitle'] = $thread->title;
	$page->blockArgs['createdBy'] = $thread->createdBy;
	$page->render();

?>
