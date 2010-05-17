<?

	require_once($installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default is 0) [string]
//opt: num - number of records per page (default is 30) [string]

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

