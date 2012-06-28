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
//opt: pagination - set to 'no' to disable paginated nav [string]

function forums_showthreads($args) {
	global $db;
	global $page;
	global $theme;

	$pageno = 1; 			//%	current page number [int]
	$num = 20; 				//%	number of threads per page [int]
	$html = '';				//%	return value [string]
	$pagination = 'yes';	//%	show HTML pagination (yes|no) [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('forumUID', $args)) { return ''; }
	if (true == array_key_exists('pageno', $args)) { $pageno = (int)$args['pageno']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pagination', $args)) { $pagination = $args['pagination']; }				

	$model = new Forums_Board($args['forumUID']);
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	count all threads on this forum
	//----------------------------------------------------------------------------------------------
	$conditions = array("board='" . $model->UID . "'");
	$totalItems = $db->countRange('forums_thread', $conditions);
	$totalPages = ceil($totalItems / $num);

	//----------------------------------------------------------------------------------------------
	//	load a page of results from the database
	//----------------------------------------------------------------------------------------------
	$start = (($pageno - 1) * $num);
	$range = $db->loadRange('forums_thread', '*', $conditions, 'updated DESC', $num, $start);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$rowBlock = $theme->loadBlock('modules/forums/views/threadrow.block.php');
	$html .= "<table noborder width='100%'>";
	foreach ($range as $row) {
		$thread = new Forums_Thread();
		$thread->loadArray($row);
		$labels = $thread->extArray();
		$html .= $theme->replaceLabels($labels, $rowBlock);
	}
	$html .= "</table>";

	$link = '%%serverPath%%forums/show/' . $model->alias . '/';
	$pagination = "[[:theme::pagination::page=" . $db->addMarkup($pageno) 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	if (0 == $totalItems) { 
		$html = "<div class='inlinequote'>No threads in this board yet.</div>";
	}

	if (($start + $num) >= $totalItems) { $html .= "<!-- end of results -->"; }

	if ('yes' == $pagination) { $html = $pagination . $html . $pagination; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
