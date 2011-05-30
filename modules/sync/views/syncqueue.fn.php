<?

//-------------------------------------------------------------------------------------------------
//|	list of items in the sync queue
//-------------------------------------------------------------------------------------------------
//opt: pageNo - results page to display, starting from 1 (int) [string]
//opt: num - maximum number of sync notices per page, default is 100 (int) [string]

function sync_syncqueue($args) {
	global $db, $user, $theme;
	$html = '';						//%	return value [string]
	$pageNo = 1;
	$num = 100;

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('pageNo', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if ($pageNo < 1) { $pageNo = 1; }

	//----------------------------------------------------------------------------------------------
	//	count sync notices and load a page of them from the database (if any)
	//----------------------------------------------------------------------------------------------
	$totalItems = $db->countRange('sync_notice', '');
	$totalPages = ceil($totalItems / $num);
	$start = (($pageNo - 1) * $num);
	$finish = ($start + $num);
	if ($finish > $totalItems) { $finish = $totalItems; }

	$range = $db->loadRange('sync_notice', '*', '', 'editedOn', $num, $start);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (0 == $totalItems) { 
		$html .= "<div class='inlinequote'>There are no outstanding sync notices at present.</div>";
		return $html; 
	}

	$table = array();
	$table[] = array('Source', 'Type', 'Peer', 'Status', 'Recieved', '[x]');

	foreach ($range as $row) {
		$link = "<a href='%%serverPath%%sync/showqueueitem/" . $row['UID'] . "'>&gt;&gt;</a>";

		$table[] = array(
			$row['source'],
			$row['type'],
			$row['peer'], 
			$row['status'],
			$row['received'],
			$link
		);

	}

	$link = '%%serverPath%%sync/showqueue/';
	$pagination = "[[:theme::pagination::page=$pageNo::total=$totalPages::link=$link:]]\n";
	$html .= $theme->arrayToHtmlTable($table, true, true);
	$html = $pagination . $html . $pagination;
	$html .= "<small><b>Showing $start to $finish of $totalItems sync notices.</b></small><br/>";
	return $html;
}

?>
