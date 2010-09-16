<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list threads in a forum
//--------------------------------------------------------------------------------------------------
//arg: forumUID - UID of a forum [string]
//opt: pageno - page number (default is 1) [string]
//opt: num - number of threads per page (default is 20) [string]

function forums_showthreads($args) {
	global $db, $page, $theme;
	$pageno = 1; 		//%	current page number [int]
	$num = 20; 			//%	number of threads per page [int]
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('forumUID', $args)) { return ''; }
	if (true == array_key_exists('pageno', $args)) { $pageno = (int)$args['pageno']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }		

	$model = new Forums_Board($args['forumUID']);
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	count all threads on this forum
	//----------------------------------------------------------------------------------------------
	$conditions = array("board='" . $model->UID . "'");
	$totalItems = $db->countRange('Forums_Thread', $conditions);
	$totalPages = ceil($totalItems / $num);

	//----------------------------------------------------------------------------------------------
	//	show the current page
	//----------------------------------------------------------------------------------------------
	$start = (($pageno - 1) * $num);
	$range = $db->loadRange('Forums_Thread', '*', $conditions, 'updated DESC', $num, $start);

	//$sql = "select * from Forums_Thread where forum='" . $fUID . "' order by updated DESC " . $limit;	
	$rowBlock = $theme->loadBlock('modules/forums/views/threadrow.block.php');

	$html .= "<table noborder>";
	foreach ($range as $row) {
		$thisThread = new Forums_Thread();
		$thisThread->loadArray($row);
		$html .= $theme->replaceLabels($thisThread->extArray(), $rowBlock);
	}

	$html .= "</table>";

	$link = '%%serverPath%%forums/show/' . $model->alias . '/';

	$pagination = "[[:theme::pagination::page=" . $db->addMarkup($pageno) 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	if (0 == $totalItems) { $html = "(no threads in this forum as yet)";	}

	return $pagination . $html . $pagination;
}

//--------------------------------------------------------------------------------------------------

?>
