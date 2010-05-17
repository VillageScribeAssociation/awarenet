<?

	require_once($installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default 0) [string]
//opt: num - number of records per page (default 30) [string]

function comments_summarylist($args) {
	if (authHas('comments', 'list', '') == false) { return false; }
	if (authHas('comments', 'view', '') == false) { return false; }

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
	$list = dbLoadRange('comments', '*', '', 'createdOn', $num, $start);
	foreach($list as $UID => $row) {
		$model = new comments();
		$model->loadArray($row);
		$html .= replaceLabels($model->extArray(), loadBlock('modules/comments/views/summary.block.php'));
	}  
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

