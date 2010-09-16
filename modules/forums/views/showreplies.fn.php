<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a thread and paginated replies
//--------------------------------------------------------------------------------------------------
//arg: threadUID - UID of a forum thread [string]
//opt: pageno - page to display, default is 1 (int) [string]
//opt: num - number of replies per page, default is 5 (int) [string]

function forums_showreplies($args) {
	global $db, $page, $theme, $user;

	$pageno = 1; 
	$num = 5; 
	$html = '';		//%	return value [string]

	if (false == array_key_exists('threadUID', $args)) { return ''; }
	if (true == array_key_exists('pageno', $args)) { $pageno = (int)$args['pageno']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }		

	//----------------------------------------------------------------------------------------------
	//	load thread model
	//----------------------------------------------------------------------------------------------
	$model = new Forums_Thread($args['threadUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: check permission here

	//----------------------------------------------------------------------------------------------
	//	count all replies in this thread
	//----------------------------------------------------------------------------------------------
	$conditions = array("thread='" . $model->UID . "'");
	$totalItems = $db->countRange('Forums_Reply', $conditions);
	$totalPages = ceil($totalItems / $num);

	//----------------------------------------------------------------------------------------------
	//	show the current page
	//----------------------------------------------------------------------------------------------
	$start = (($pageno - 1) * $num);
	$range = $db->loadRange('Forums_Reply', '*', $conditions, 'createdOn ASC', $num, $start);

	//$sql = "select * from Forums_Reply "
	//	 . "where thread='" . $model->UID . "' "
	//	 . "order by createdOn ASC " . $limit;	

	$block = $theme->loadBlock('modules/forums/views/reply.block.php');

	foreach ($range as $row) {
		$reply = new Forums_Reply();
		$reply->loadArray($row);
		$ext = $reply->extArray();
		$ext['threadTitle'] = $model->title;
		$html .= $theme->replaceLabels($ext, $block);
	}

	$link = '%%serverPath%%forums/showthread/' . $model->alias . '/';

	$pagination = "[[:theme::pagination::page=" . $db->addMarkup($pageno) 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	if (0 == $totalItems) { $html = "(no replies yet)";	}

	return $pagination . $html . $pagination;

}

//--------------------------------------------------------------------------------------------------

?>
