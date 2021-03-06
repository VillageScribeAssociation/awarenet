<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list (the 'moblog itself')
//--------------------------------------------------------------------------------------------------
//arg: schoolUID - UID of a Schools_School object [string]
//opt: page - page no to display, default is 1 (int) [string]
//opt: num - number of records per page, default is 30 (int) [string]
//opt: pagination - show pagination bar, default is 'yes' (yes|no) [string]

function moblog_schoolsummarylist($args) {
	global $kapenta;
	global $kapenta;
	global $kapenta;
	global $theme;
	global $kapenta;
	global $aliases;

	$start = 0;
	$num = 30;
	$pageNo = 1;
	$schoolUID = '';
	$pagination = 'yes';		//%	show pagination (yes|no) [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('moblog', 'moblog_post', 'show')) { return ''; }

	if (false == array_key_exists('schoolUID', $args)) { return '(No schoolUID)'; }

	$schoolUID = $args['schoolUID'];
	if (false == $kapenta->db->objectExists('schools_school', $schoolUID)) { return 'No such schoolUID'; }

	if (true == array_key_exists('pagination', $args)) { $pagination = $args['pagination']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pageNo', $args)) { $args['page'] = $args['pageNo']; }
	if (true == array_key_exists('page', $args)) 
		{ $pageNo = $args['page']; $start = ($pageNo - 1) * $num; }

	$schoolRa = $aliases->getDefault('schools_school', $schoolUID);

	//----------------------------------------------------------------------------------------------
	//	count visible posts
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "school='" . $kapenta->db->addMarkup($schoolUID) . "'";
	$conditions[] = " (published='yes' or createdBy='" . $kapenta->user->UID . "') ";

	$totalItems = $kapenta->db->countRange('moblog_post', $conditions);
	$totalPages = ceil($totalItems / $num);

	$link = '%%serverPath%%moblog/school/' . $schoolRa . '/';
	$pagination = "[[:theme::pagination::page=" . $kapenta->db->addMarkup($pageNo) 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	//----------------------------------------------------------------------------------------------
	//	load a page worth of objects from the database
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('moblog_post', '*', $conditions, 'createdOn DESC', $num, $start);

	$block = $theme->loadBlock('modules/moblog/views/summary.block.php');

	foreach($range as $UID => $row) {
		$model = new Moblog_Post();
		$model->loadArray($row);
		$labels = $model->extArray();
		$labels['rawblock64'] = base64_encode('[[:moblog::summary::UID=' . $row['UID'] . ':]]');

		$html .= $theme->replaceLabels($labels, $block);

		$channel = 'post-' . $model->UID;
		// $kapenta->page->setTrigger('moblog', $channel, "[[:moblog::summary::UID=" . $row['UID'] . ":]]");
	}

	if (($start + $num) >= $totalItems) { $html .= "<!-- end of results -->"; }

	if ('yes' == $pagination) { $html = $pagination . $html . $pagination; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
