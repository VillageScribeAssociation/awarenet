<?

	require_once($installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of comment [string]

function comments_summary($args) {
	if (authHas('comments', 'view', '') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$model = new Comment($args['UID']);
		$html = replaceLabels($model->extArray(), loadBlock('modules/comments/views/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------

?>

