<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list (the 'moblog itself')
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default is 0) [string]
//opt: num - number of records per page (default is 30) [string]
//opt: pagination - set to 'no' to disable page nav bar (yes|no) [string]
//opt: userUID - constrain to posts by this user (ref:Users_User) [string]
//opt: schoolUID - constrain to posts from this school (ref:Schools_School) [string]

function moblog_summarylist($args) {
	global $page;
	global $db;
	global $user;
	global $theme;
	global $page;

	$pageNo = 1;				//%	results page to display [int]
	$num = 30;					//%	number of items per page [int]
	$start = 0;					//%	offset in database results [int]
	$pagination = 'yes';		//%	show pagination [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('moblog', 'moblog_post', 'show')) { return ''; }

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pagination', $args)) { $pagination = $args['pagination']; }
	if (true == array_key_exists('page', $args)) { 
		$pageNo = $args['page']; $start = ($pageNo - 1) * $num; 
	}

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
		//$model = new Moblog_Post();
		//$model->loadArray($row);
		//$labels = $model->extArray();
		//$labels['rawblock64'] = base64_encode('[[:moblog::summary::UID=' . $row['UID'] . ':]]');
		//$html .= $theme->replaceLabels($labels, $block);
		//$channel = 'post-' . $model->UID;
		//$page->setTrigger('moblog', $channel, "[[:moblog::summary::UID=" . $row['UID'] . ":]]");

		$html .= "[[:moblog::summary::UID=" . $row['UID'] . ":]]";

	}

	if (($start + $num) >= $totalItems) { $html .= "<!-- end of results -->"; }
	if ('yes' == $pagination) { $html = $pagination . $html . $pagination; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
