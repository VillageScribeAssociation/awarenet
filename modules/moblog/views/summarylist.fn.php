<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list (the 'moblog itself')
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default is 0) [string]
//opt: num - number of records per page (default is 30) [string]

function moblog_summarylist($args) {
	global $page, $db, $user, $theme, $page;
	$start = 0;
	$num = 30;
	$pageNo = 1;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('moblog', 'moblog_post', 'show')) { return ''; }

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('page', $args)) 
		{ $pageNo = $args['page']; $start = ($pageNo - 1) * $num; }

	//----------------------------------------------------------------------------------------------
	//	count visible posts
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = " (published='yes' or createdBy='" . $user->UID . "') ";
	if (true == array_key_exists('userUID', $args)) 
		{ $conditions[] = "createdBy='" . $db->addMarkup($args['userUID']) . "'"; }

	if (true == array_key_exists('schoolUID', $args)) 
		{ $conditions[] = "school='" . $db->addMarkup($args['schoolUID']) . "'"; }

	$totalItems = $db->countRange('moblog_post', $conditions);
	$totalPages = ceil($totalItems / $num);

	$link = '%%serverPath%%moblog/';
	$pagination = "[[:theme::pagination::page=" . $db->addMarkup($pageNo) 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	//----------------------------------------------------------------------------------------------
	//	load a page worth of objects from the database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('moblog_post', '*', $conditions, 'createdOn DESC', $num, $start);

	$block = $theme->loadBlock('modules/moblog/views/summary.block.php');

	foreach($range as $UID => $row) {
		$model = new Moblog_Post();
		$model->loadArray($row);
		$labels = $model->extArray();
		$labels['rawblock64'] = base64_encode('[[:moblog::summary::UID=' . $row['UID'] . ':]]');

		$html .= $theme->replaceLabels($labels, $block);

		$channel = 'post-' . $model->UID;
		$page->setTrigger('moblog', $channel, "[[:moblog::summary::UID=" . $row['UID'] . ":]]");
	}

	$html = $pagination . $html . $pagination;

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
