<?

	require_once($installPath . 'modules/announcements/models/announcements.mod.php');

//--------------------------------------------------------------------------------------------------
//	summary list
//--------------------------------------------------------------------------------------------------
// * $args['page'] = page no to display
// * $args['num'] = number of records per page

function announcements_summarylist($args) {
	if (authHas('announcements', 'show', '') == false) { return false; }

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
	$list = dbLoadRange('announcements', '*', '', 'createdOn', $num, $start);
	foreach($list as $UID => $row) {
		$model = new announcements();
		$model->loadArray($row);
		$html .= replaceLabels($model->extArray(), loadBlock('modules/announcements/views/summary.block.php'));
	}  
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>