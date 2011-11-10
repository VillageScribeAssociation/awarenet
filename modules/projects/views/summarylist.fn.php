<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list or recent projects order by 'editedOn' date
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display, default is 1 (int) [string]
//opt: pageNo - overrides page (int) [string]
//opt: num - number of records per page (default is 30) [string]
//opt: pagination - set to 'no' to disable page nav bar (yes|no) [string]

function projects_summarylist($args) {
	global $page;
	global $db;
	global $user;
	global $theme;
	global $page;

	$start = 0;					//%	index of first item in result set [int]
	$num = 5;					//%	number of items per page [int]
	$pageNo = 1;				//%	results page to display [int]
	$html = '';					//%	return value [string]
	$pagination = 'yes';		//%	show pagination (yes|no) [string]
	$orderBy = 'editedOn';		//%	list order on this field [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('projects', 'projects_project', 'show')) { return ''; }

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pagination', $args)) { $pagination = $args['pagination']; }
	if (true == array_key_exists('pageNo', $args)) { $args['page'] = $args['pageNo']; }
	if (true == array_key_exists('page', $args)) { 
		$pageNo = $args['page']; 
		$start = ($pageNo - 1) * $num; 
	}

	//----------------------------------------------------------------------------------------------
	//	count visible projects
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$totalItems = $db->countRange('projects_project', $conditions);
	$totalPages = ceil($totalItems / $num);

	$link = '%%serverPath%%projects/';
	$pagination = "[[:theme::pagination::page=" . $db->addMarkup($pageNo) 
				. "::total=" . $totalPages . "::link=" . $link . ":]]\n";

	//----------------------------------------------------------------------------------------------
	//	load a page worth of objects from the database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('projects_project', '*', $conditions, 'editedOn DESC', $num, $start);
	$block = $theme->loadBlock('modules/projects/views/summary.block.php');

	foreach($range as $UID => $row) {
		$model = new Projects_Project();
		$model->loadArray($row);
		$labels = $model->extArray();
		$labels['rawblock64'] = base64_encode('[[:projects::summary::UID=' . $row['UID'] . ':]]');

		$html .= $theme->replaceLabels($labels, $block);

		$channel = 'project-' . $model->UID;
		$page->setTrigger('projects', $channel, "[[:projects::summary::UID=" . $row['UID'] . ":]]");
	}

	if (($start + $num) > $totalItems) { $html .= "<!-- end of results -->"; }

	if ('yes' == $pagination) { $html = $pagination . $html . $pagination; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
