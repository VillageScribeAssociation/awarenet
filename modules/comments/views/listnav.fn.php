<?

	require_once($installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all most recent x comments owned by a particular record on a given module, naarow for nav
//--------------------------------------------------------------------------------------------------
//arg: refUID - record which owns the comments [string]
//arg: refModule - module which owns the record [string]
//opt: num - number of records per page (default 10) [string]

function comments_listnav($args) {
	if (authHas('comments', 'list', '') == false) { return false; }
	if (authHas('comments', 'view', '') == false) { return false; }
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }

	$num = 10;
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$sql = "select * from comments "
		 . "where refModule='" . sqlMarkup($args['refModule']) . "' "
		 . "and refUID='" . sqlMarkup($args['refUID']) . "' "
		 . "order by createdOn DESC limit " . sqlMarkup($num) . "";

	$blockFile = 'modules/comments/views/summarynav.block.php';

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$model = new comment();
			$model->loadArray($row);
			$html .= replaceLabels($model->extArray(), loadBlock($blockFile));
		}  
	} else {
		$html .= "(no comments at present)<br/>";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

