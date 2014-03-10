<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list
//--------------------------------------------------------------------------------------------------
//opt: page - page no to display (default 0) [string]
//opt: num - number of records per page (default 30) [string]

function comments_summarylist($args) {
		global $kapenta;
		global $page;
		global $theme;

	$num = 30;							//%	number of items per page [int]
	$pageNo = 1;						//%	page number, starts from 1 [int]
	$start = 0;							//%	starting position within SQL results [int]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	arguments
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('comments', 'comment_comment', 'list')) { return ''; }
	if (false == $user->authHas('comments', 'comment_comment', 'show')) { return ''; }

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('page', $args)) { 
		$pageNo = (int)$args['page']; 
		$start = ($pageNo - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	query database and make block
	//----------------------------------------------------------------------------------------------
	$list = $kapenta->db->loadRange('comments_comment', '*', '', 'createdOn', $num, $start);
	$block = $theme->loadBlock('modules/comments/views/summary.block.php');

	foreach($list as $UID => $row) {
		$model = new Comments_Comment();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
