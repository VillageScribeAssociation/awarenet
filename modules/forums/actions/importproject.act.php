<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	copy a project across to the forum section
//--------------------------------------------------------------------------------------------------
//postarg: project - alias or UID of a Projects_Project object [string]
//postarg: board - alias or UID of a Forums_Board object [string]

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	check that the project and board both exist
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('project', $_POST)) { $page->do404('project not specified'); }
	if (false == array_key_exists('board', $_POST)) { $page->do404('board not specified'); }

	$project = new Projects_Project($_POST['project']);
	if (false == $project->loaded) { $page->do404('project not found'); }

	$board = new Forums_Board($_POST['board']);
	if (false == $board->loaded) { $page->do404('board not found'); }

	//----------------------------------------------------------------------------------------------
	//	make new forum thread
	//----------------------------------------------------------------------------------------------
	$content = $theme->expandBlocks('[[:projects::show::raUID='. $project->UID .':]]', '')
		. "<br/><div class='inlinequote'>Moved from projects module by "
		. "[[:users::namelink::userUID=" . $user->UID . ":]] on " . $db->datetime() . ".</div>";

	$content = str_replace("\n", '', $content);
	$content = str_replace("\r", '', $content);
	$content = str_replace('<h1>', '<b>(original title: ', $content);
	$content = str_replace('</h1>', ')</b> ', $content);
	$content = str_replace('/projects/', '/forums/showthread/', $content);
	$content = str_replace('>[edit abstract]<', '><', $content);
	$content = str_replace('>[edit section]<', '><', $content);

	$thread = new Forums_Thread();
	$thread->board = $board->UID;
	$thread->title = $project->title;
	$thread->content = $content;
	$thread->createdOn = $project->createdOn;
	$thread->createdBy = $project->createdBy;
	$report = $thread->save();

	if ('' == $report) { 
		$session->msg('Imported project: ' . $project->title, 'ok');

	} else {
		$session->msg('Could not import project: ' . $project->title, 'bad');
		$page->do302('admin/');
	}

	//----------------------------------------------------------------------------------------------
	//	copy any comments as replies
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='projects'";
	$conditions[] = "refUID='" . $db->addMarkup($project->UID) . "'";
	$range = $db->loadRange('comments_comment', '*', $conditions);

	foreach($range as $item) {
		$comment = $item['comment']
		. "<br/><div class='inlinequote'>Moved from comments module by "
		. "[[:users::namelink::userUID=" . $user->UID . ":]] on " . $db->datetime() . ".</div>\n";

		$reply = new Forums_Reply();
		$reply->forum = $board->UID;
		$reply->thread = $thread->UID;
		$reply->content = $comment;
		$reply->createdOn = $item['createdOn'];
		$reply->createdBy = $item['createdBy'];
		$report = $reply->save();

		if ('' == $report) {
			$msg = 'Imported comment by '
				 . '[[:users::namelink::userUID=' . $item['createdBy'] . ':]].';
			$session->msg($msg, 'ok');
		} else {
			$msg = 'Could not import comment by '
				 . '[[:users::namelink::userUID='. $item['createdBy'] .':]].';
			$session->msg($msg, 'bad');
		}
	}

	//----------------------------------------------------------------------------------------------
	//	redirect to the new thread
	//----------------------------------------------------------------------------------------------
	$page->do302('forums/showthread/' . $thread->UID);

?>
