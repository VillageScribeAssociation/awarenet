<?

	require_once($installPath . 'modules/comments/models/comments.mod.php');

//--------------------------------------------------------------------------------------------------
//	summary
//--------------------------------------------------------------------------------------------------
//arg: UID - uid of a comment

function comments_summarynav($args) {
	if (authHas('comments', 'view', '') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$model = new Comment($args['UID']);
		$html = replaceLabels($model->extArray(), loadBlock('modules/comments/views/summarynav.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------

?>
