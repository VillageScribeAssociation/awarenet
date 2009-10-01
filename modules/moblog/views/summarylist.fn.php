<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//	summary list (the 'moblog itself')
//--------------------------------------------------------------------------------------------------
// * $args['page'] = page no to display
// * $args['num'] = number of records per page

function moblog_summarylist($args) {
	global $user;
	if (authHas('moblog', 'list', '') == false) { return false; }
	if (authHas('moblog', 'view', '') == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	arguments
	//----------------------------------------------------------------------------------------------
	$start = 0; $num = 30; $page = 1;

	if (array_key_exists('num', $args)) { $num = $args['num']; }
	if (array_key_exists('page', $args)) { 
		$page = $args['page']; 
		$start = ($page - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = " (published='yes' or createdBy='" . $user->data['UID'] . "') ";
	if (array_key_exists('userUID', $args) == true) 
		{ $conditions[] = "createdBy='" . sqlMarkup($args['userUID']) . "'"; }

	if (array_key_exists('schoolUID', $args) == true) 
		{ $conditions[] = "school='" . sqlMarkup($args['schoolUID']) . "'"; }

	$list = dbLoadRange('moblog', '*', $conditions, 'createdOn DESC', $num, $start);
	foreach($list as $UID => $row) {
		$model = new moblog();
		$model->loadArray($row);
		$html .= replaceLabels($model->extArray(), loadBlock('modules/moblog/views/summary.block.php'));
	}  
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>