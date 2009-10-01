<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	list threads in a forum
//--------------------------------------------------------------------------------------------------
// * $args['forumUID'] = UID of a forum
// * $args['pageno'] = page number
// * $args['num'] = number of threads per page

function forums_showthreads($args) {
	$pageno = 1; $num = 20; $html = '';
	if (array_key_exists('forumUID', $args) == false) { return false; }
	if (array_key_exists('pageno', $args) == true) { $pageno = $args['pageno']; }
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }		
	$fUID = sqlMarkup($args['forumUID']);
	$fRa = raGetDefault('forums', $fUID);

	//----------------------------------------------------------------------------------------------
	//	count all threads on this forum
	//----------------------------------------------------------------------------------------------
	$sql = "select count(UID) as numRecords from forumthreads where forum='" . $fUID . "'";
	$result = dbQuery($sql);
	$row = sqlRMArray(dbFetchAssoc($result));
	$total = ceil($row['numRecords'] / $num);

	//----------------------------------------------------------------------------------------------
	//	show the current page
	//----------------------------------------------------------------------------------------------
	$limit = "limit " . (($pageno - 1) * $num) . ", ". sqlMarkup($num);
	$sql = "select * from forumthreads where forum='" . $fUID . "' order by updated DESC " . $limit;	
	$result = dbQuery($sql);

	$rowBlock = loadBlock('modules/forums/views/threadrow.block.php');

	$html .= "<table noborder>";
	while ($row = dbFetchAssoc($result)) {
		$thisThread = new ForumThread();
		$thisThread->loadArray(sqlRMArray($row));
		$html .= replaceLabels($thisThread->extArray(), $rowBlock);
		
	}
	$html .= "</table>";

	$link = '%%serverPath%%forums/show/' . $fRa . '/';

	$pagination .= "[[:theme::pagination::page=" . sqlMarkup($page) 
				. "::total=" . $total . "::link=" . $link . ":]]\n";

	if (0 == $total) { $html = "(no threads in this forum as yet)";	}

	return $pagination . $html . $pagination;



}

//--------------------------------------------------------------------------------------------------

?>